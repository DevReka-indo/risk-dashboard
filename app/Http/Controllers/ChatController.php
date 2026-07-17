<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DepartemenRiskRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    protected $riskRepo;

    public function __construct(DepartemenRiskRepository $riskRepo)
    {
        $this->riskRepo = $riskRepo;
    }

    public function ask(Request $request)
    {
        $userMessage = $request->input('message');

        try {
            // 1. Ambil GROQ API Key dari .env
            $apiKey = env('GROQ_API_KEY');
            $groqUrl = 'https://api.groq.com/openai/v1/chat/completions';

            // 2. Format Payload Standar OpenAI / Groq
            $payload = [
                'model' => 'llama-3.3-70b-versatile', // Model terbaik Groq yang mendukung Function Calling
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Anda adalah asisten cerdas Manajemen Risiko PT Rekaindo. Jawab dengan ramah, profesional, dan ringkas.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $userMessage
                    ]
                ],
                'tools' => [
                    [
                        'type' => 'function',
                        'function' => [
                            'name' => 'get_highest_risk',
                            'description' => 'Gunakan fungsi ini jika user menanyakan tentang risiko paling tinggi, paling kritis, atau top risk.',
                            'parameters' => [
                                'type' => 'object',
                                'properties' => (object)[] // Aman dari bug PHP array/object kosong!
                            ]
                        ]
                    ]
                ]
            ];

            // Kirim request pertama ke Groq dengan Bearer Token
            $response = Http::withToken($apiKey)->timeout(60)->post($groqUrl, $payload);
            $responseData = $response->json();

            // Proteksi Error API
            if (isset($responseData['error'])) {
                return response()->json([
                    'reply' => 'Error Groq API: ' . $responseData['error']['message']
                ], 200);
            }

            // Ekstrak pesan dan cek apakah AI memanggil tool
            $aiMessage = $responseData['choices'][0]['message'] ?? null;
            $toolCalls = $aiMessage['tool_calls'] ?? null;

            // Jika Groq AI memutuskan untuk memanggil fungsi database
            if ($toolCalls && $toolCalls[0]['function']['name'] === 'get_highest_risk') {

                $toolCallId = $toolCalls[0]['id']; // Ambil ID panggillan tool

                $highestRisk = \App\Models\DepMonitoring::with(['unitKerja', 'levelRisiko'])
                    ->orderByDesc('value')
                    ->first();

                if (!$highestRisk) {
                    return response()->json([
                        'reply' => 'Saat ini belum ada data risiko yang tercatat di dalam sistem kami.'
                    ]);
                }

                $dataUntukAI = [
                    'peristiwa' => $highestRisk->risk_event_deta ?? $highestRisk->risk_event_detail ?? 'Tidak ada detail',
                    'unit_kerja' => $highestRisk->unitKerja->nama_unit ?? 'Tidak diketahui',
                    'nilai_risiko' => $highestRisk->value,
                    'level' => $highestRisk->levelRisiko->nama_level ?? 'Tidak diketahui',
                    'status_penanganan' => $highestRisk->penanganan
                ];

                // Siapkan Request Kedua ke Groq
                $payload2 = $payload;

                // Riwayat 1: Selipkan jawaban AI sebelumnya yang meminta pemanggilan tool
                $payload2['messages'][] = $aiMessage;

                // Riwayat 2: Masukkan hasil data dari DB dengan role 'tool'
                $payload2['messages'][] = [
                    'role' => 'tool',
                    'tool_call_id' => $toolCallId,
                    'name' => 'get_highest_risk',
                    'content' => json_encode($dataUntukAI) // Di Groq wajib string JSON
                ];

                // Minta Groq merangkai kalimat akhir
                $response2 = Http::withToken($apiKey)->timeout(60)->post($groqUrl, $payload2);
                $response2Data = $response2->json();

                if (isset($response2Data['error'])) {
                    return response()->json([
                        'reply' => 'Error Groq API (Request 2): ' . $response2Data['error']['message']
                    ], 200);
                }

                $finalText = $response2Data['choices'][0]['message']['content'] ?? 'Maaf, saya tidak dapat merangkai kalimat saat ini.';

                return response()->json([
                    'reply' => $finalText
                ]);
            }

            // Jika AI hanya menjawab chat biasa tanpa panggil DB
            $textReply = $aiMessage['content'] ?? 'Tidak ada jawaban dari AI.';

            return response()->json([
                'reply' => $textReply
            ]);

        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());

            return response()->json([
                'reply' => 'Terjadi error di server: ' . $e->getMessage()
            ], 200);
        }
    }
}
