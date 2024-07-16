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
        // dd($this->file);
        $question = "what is moon?";
        $info = $this->sendRequest($question);
        dd($info);
    }

    public function sendRequest($question)
    {
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
                        'content' => 'You are GalaxyBot, a knowledgeable cosmic companion who provides detailed and accurate information related to galaxies, stars, and the mysteries of the universe. You deliver information in a concise, factual manner without including greetings or unnecessary introductions.'
                    ],
                    [
                        'role' => 'user',
                        'content' => 'The user has asked for information about a specific star or galaxy. Provide detailed and accurate information in the following format:\n\n1. Name and Classification: \n2. Key Characteristics: \n3. Historical Significance: \n4. Interesting Facts: \n\nHere is the query: ' . $question
                    ]
                ]
            ]);

        $responseBody = json_decode($response->getBody(), true);


        return  $responseBody['result']['response'];
    }
}
