<form method="POST" action="/reg">
    @csrf
    @error('email')
    {{ $message }}<br>
    @enderror
    <input type="text" name="name" value="{{ old('name') }}" required>

    NAME<br>

    <input type="email" name="email" value="{{ old('email') }}" required>

    EMAIL<br>

    <input type="password" name="password" required>

    PASSWORD<br>

    <input type="password" name="password_confirmation" required>
    CONFIRM PASSWORD<br>
    <input type="submit" value="Зарегистрироваться">
</form>
