<x-app-layout>
    <div class="flex flex-col items-center justify-center h-screen text-center gap-6">
        <h1 class="text-6xl font-bold text-[#E8A020]">403</h1>
        <p class="text-xl text-black/80">{{ $exception->getMessage() }}</p>
        <a href="{{ route('home') }}" class="px-6 py-3 bg-[#E8A020] text-white rounded-md font-semibold hover:bg-[#c17b16] transition-colors">
            Return to home page
        </a>
    
</x-app-layout>