<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Http;



class UploadImage extends Component
{
    use WithFileUploads;
    public $file = "";

    public function render()
    {
        return view('livewire.upload-image');
    }

    public function uploadImage()
    {
        $info = $this->createStoryFromImg($this->file);
        // dd($info['result']['description']);
        $text = $this->sendRequest($info['result']['description']);
        dd($text);
        // return $this->redirect('/info');
    }

    public function sendRequest($text)
    {
        try {

            $authorizationToken = config('cloudflare.api_key');
            $accountId = config('cloudflare.account_id');

            $url = 'https://api.cloudflare.com/client/v4/accounts/' . $accountId . '/ai/run/@cf/meta/llama-2-7b-chat-fp16';

            $response = Http::withToken(
                $authorizationToken
            )
                ->post($url, [
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are story teller. you have to create a short story from the given text.'
                        ],
                        [
                            'role' => 'user',
                            'content' => 'The user has provided text from that you have to create a short story for children ' . $text
                        ]
                    ]
                ]);

            $responseBody = json_decode($response->getBody(), true);
            // dd($responseBody['result']['response']);
            return  $responseBody['result']['response'];
        } catch (\Exception $e) {
            // dd($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createStoryFromImg($file)
    {
        try {
            $imageData = file_get_contents($file->getRealPath());
            $imageArray = unpack('C*', $imageData);

            // Prepare input for the AI service
            $input = [
                'image' => array_values($imageArray),
                'prompt' => 'Generate a caption for this image by extracting all the details.',
                'max_tokens' => 512,
            ];

            return $this->callAiService($input);
        } catch (\Exception $e) {
            // dd($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function callAiService($input)
    {
        $authorizationToken = config('cloudflare.api_key');
        $accountId = config('cloudflare.account_id');
        $url = 'https://api.cloudflare.com/client/v4/accounts/' . $accountId . '/ai/run/@cf/llava-hf/llava-1.5-7b-hf';

        $response = Http::withToken(
            $authorizationToken
        )
            ->post($url, $input);

        if ($response->successful()) {
            return $response->json();
        } else {
            return ['error' => 'Failed to get response from AI service'];
        }
    }
}
