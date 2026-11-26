<x-layouts.app title="Login">
    <h1>Login</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <x-form-input name="email" label="Email" type="email" />
        <x-form-input name="password" label="Password" type="password" />

        <button type="submit" class="btn">Login</button>
    </form>
</x-layouts.app>
