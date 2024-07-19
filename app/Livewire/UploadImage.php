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
        $sentiments = $this->sendRequest($info['result']['description']);

        return redirect()->back()->with(['text' => $info['result']['description'], 'sentiments' => $sentiments]);
    }

    public function sendRequest($text)
    {
        try {

            $authorizationToken = config('cloudflare.api_key');
            $accountId = config('cloudflare.account_id');

            $url = 'https://api.cloudflare.com/client/v4/accounts/' . $accountId . '/ai/run/@cf/huggingface/distilbert-sst-2-int8';

            $response = Http::withToken(
                $authorizationToken
            )
                ->post($url, [
                    'text' => $text
                ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createStoryFromImg($file)
    {
        try {
            if (!empty($file)) {

                $imageData = file_get_contents($file->getRealPath());
                $imageArray = unpack('C*', $imageData);

                // Prepare input for the AI service
                $input = [
                    'image' => array_values($imageArray),
                    'prompt' => 'Generate a caption for this image by extracting all the details.',
                    'max_tokens' => 512,
                ];

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
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
