<form method="POST" action="/log">
    @csrf
    @error('error')
    {{ $message }}<br>
    @enderror
    <input type="text" name="name" value="{{ old('name') }}" required>

    LOGIN<br>

    <input type="password" name="password" required>

    PASSWORD<br>

    <input type="submit" value="Login">
</form>
