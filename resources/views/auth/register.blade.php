<x-layouts.app title="Register">
    <h1>Register</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <x-form-input name="name" label="Name" />
        <x-form-input name="email" label="Email" type="email" />
        <x-form-input name="password" label="Password" type="password" />
        <x-form-input name="password_confirmation" label="Confirm Password" type="password" />

        <button type="submit" class="btn">Register</button>
    </form>
</x-layouts.app>
