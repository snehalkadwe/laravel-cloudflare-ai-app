<div class="h-screen p-10">

    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">

    <div class="border border-pink-700 h-full flex flex-col justify-between relative">
        <div class="text-center">
            <h1 class="text-center italic font-bold m-4">Text generated from an image</h1>
            @if (session()->get('text'))
            <div class="text-justify p-6 mt-2">
                <p class="py-4">{{ session()->get('text') }}</p>
                <h2 class="italic font-bold">Sentiment analysis of text generated by an image</h2>
                <p class="py-2">
                    <b>{{ session()->get('sentiments')['result'][0]['label'] }}</b> -
                    {{ session()->get('sentiments')['result'][0]['score'] }}
                </p>
                <p class="py-2">
                    <b>{{ session()->get('sentiments')['result'][1]['label'] }}</b> -
                    {{ session()->get('sentiments')['result'][1]['score'] }}
                </p>
            </div>
            @endif
        </div>
        <div class="text-center p-6">
            <form class="mt-8 space-y-3 inline-block relative" method="post" enctype="multipart/form-data"
                wire:submit.prevent="uploadImage">
                <label for="file-upload" class="block text-gray-700 font-bold mb-2">Upload an image:</label>
                <input type="file" name="file" id="file-upload" wire:model="file" class="hidden">
                <label for="file-upload"
                    class="inline-block px-4 py-2 bg-blue-500 text-white font-bold rounded-lg cursor-pointer hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Choose File
                </label>
                <button type="submit"
                    class="ml-4 px-4 py-2 bg-green-500 text-white font-bold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Upload
                </button>
            </form>
        </div>
        <div class="absolute bottom-0 w-full p-6">
            <form class="space-y-3 text-center" method="post" enctype="multipart/form-data"
                wire:submit.prevent="uploadImage">
                <label for="file-upload" class="block text-gray-700 font-bold mb-2">Upload an image:</label>
                <input type="file" name="file" id="file-upload" wire:model="file" class="hidden">
                <label for="file-upload"
                    class="inline-block px-4 py-2 bg-blue-500 text-white font-bold rounded-lg cursor-pointer hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Choose File
                </label>
                <button type="submit"
                    class="ml-4 px-4 py-2 bg-green-500 text-white font-bold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Upload
                </button>
            </form>
        </div>
    </div>
</div>
