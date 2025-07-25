@if (Route::has('login'))
    <nav class="flex items-center justify-between p-4 bg-white shadow-md z-50">
        <h1 class="text-xl font-bold text-blue-600">Gincaneiros</h1>
        <div class="flex items-center gap-4">
            @auth
                <!-- <a href="{{ route('profile.edit') }}" class="btn">Profile</a> -->
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn">Log Out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn">Register</a>
                @endif
            @endauth
        </div>
    </nav>
@endif
