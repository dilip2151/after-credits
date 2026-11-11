<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Movie Watchlist</title>
</head>
<body>
    <main>
        <h1>Movie Watchlist</h1>
<form method="POST" action="{{ route('watchlist.store') }}">
    @csrf

    <label for="title">Film title</label>
    <input id="title" name="title" type="text"
           value="{{ old('title') }}" maxlength="255" required>

    @error('title')
        <p>{{ $message }}</p>
    @enderror

    <button type="submit">Add film</button>
</form>
        @if ($items->isEmpty())
            <p>Your watchlist is empty.</p>
        @else
            <ul>
                @foreach ($items as $item)
                    <li>{{ $item->title }} ({{ $item->watched ? 'Watched' : 'Unwatched' }})</li>
                @endforeach
            </ul>
        @endif
    </main>
</body>
</html>