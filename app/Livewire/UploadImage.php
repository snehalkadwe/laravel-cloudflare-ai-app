<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Http;

class UploadImage extends Component
{
    use WithFileUploads;

    public $file = "";
    public $image_url;

    public function render()
    {
        return view('livewire.upload-image');
    }

    public function uploadImage()
    {
        $this->validate([
            'file' => 'image|max:1024', // 1MB Max
        ]);

        $image = $this->file;
        $imagePath = $image->store('images', 'public');
        $this->image_url = '/storage/' . $imagePath;

        // Assuming you are using session to store this data
        session()->put('image_url', $this->image_url);

        $info = $this->createTextFromImg($this->file);
        $sentiments = $this->sentimentAnalysis($info['result']['description']);

        return redirect()->back()->with(['text' => $info['result']['description'], 'sentiments' => $sentiments]);
    }

    public function sentimentAnalysis($text)
    {
        try {

            $authorizationToken = config('cloudflare.api_key');
            $accountId = config('cloudflare.account_id');
            $baseURL = config('cloudflare.url');
            $url = $baseURL . $accountId . '/ai/run/@cf/huggingface/distilbert-sst-2-int8';

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

    public function createTextFromImg($img)
    {
        try {
            if (!empty($img)) {

                $imageData = file_get_contents($img->getRealPath());
                $imageArray = unpack('C*', $imageData);

                // Prepare input for the AI service
                $input = [
                    'image' => array_values($imageArray),
                    'prompt' => 'Generate a caption for this image by extracting all the details.',
                    'max_tokens' => 512,
                ];

                $authorizationToken = config('cloudflare.api_key');
                $accountId = config('cloudflare.account_id');
                $baseURL = config('cloudflare.url');
                $url = $baseURL . $accountId . '/ai/run/@cf/llava-hf/llava-1.5-7b-hf';

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

    public function deleteExistingImage()
    {
        if (session()->get('image_url')) {
            $imagePath = storage_path('app/public/' . basename(session()->get('image_url')));
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            session()->forget('image_url');
        }
    }
}
