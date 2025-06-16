<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ZoomService
{
    protected $clientId;
    protected $clientSecret;
    protected $accountId;

    public function __construct()
    {
        $this->clientId = config('services.zoom.client_id');
        $this->clientSecret = config('services.zoom.client_secret');
        $this->accountId = config('services.zoom.account_id');
    }

    protected function getAccessToken()
    {
        return Cache::remember('zoom_access_token', 3500, function () {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'account_credentials',
                    'account_id' => $this->accountId,
                ]);

            if (!$response->successful()) {
                throw new \Exception('Không thể lấy access token từ Zoom: ' . $response->body());
            }

            return $response->json()['access_token'];
        });
    }

    public function taoSuKienZoom(array $data)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->asJson()
            ->post('https://api.zoom.us/v2/users/me/meetings', [
                'topic' => $data['topic'] ?? 'Không có tiêu đề',
                'type' => 2,
                'start_time' => $data['start_time'] ?? now()->addMinutes(15)->toIso8601String(),
                'duration' => $data['duration'] ?? 30,
                'timezone' => 'Asia/Ho_Chi_Minh',
                'password' => $data['password'] ?? '',
                'agenda' => $data['agenda'] ?? 'Không có mô tả',
                'settings' => [
                    'host_video' => true,
                    'participant_video' => true,
                    'mute_upon_entry' => true,
                    'join_before_host' => true,
                    'waiting_room' => false,
                    'meeting_authentication' => true,
                    'embed_password_in_join_link' => false,
                ],
            ]);

        if (!$response->successful()) {
            throw new \Exception('Tạo cuộc họp thất bại: ' . $response->body());
        }

        return $response->json();
    }

    public function updateMeeting(string $meetingId, array $data)
    {
        $accessToken = $this->getAccessToken();

        $payload = [
            'topic' => $data['topic'] ?? 'Cập nhật tiêu đề',
            'start_time' => $data['start_time'] ?? now()->addMinutes(30)->toIso8601String(),
            'duration' => $data['duration'] ?? 30,
            'agenda' => $data['agenda'] ?? 'Cập nhật mô tả',
            'timezone' => 'Asia/Ho_Chi_Minh',
        ];

        $response = Http::withToken($accessToken)
            ->patch("https://api.zoom.us/v2/meetings/{$meetingId}", $payload);

        if (!$response->successful()) {
            throw new \Exception('Cập nhật cuộc họp thất bại: ' . $response->body());
        }

        return $response->json();
    }

    public function xoaSuKienZoom(string $meetingId)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->delete("https://api.zoom.us/v2/meetings/{$meetingId}");

        if (!$response->successful()) {
            throw new \Exception("Xoá cuộc họp thất bại: " . $response->body());
        }

        return ['success' => true, 'message' => 'Cuộc họp đã được xoá.'];
    }
}
