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
                    <li>
                        {{ $item->title }}
                        (<span data-watch-status tabindex="-1">{{ $item->watched ? 'Watched' : 'Unwatched' }}</span>)

                        @unless ($item->watched)
                            <form method="POST"
                                  action="{{ route('watchlist.mark-watched', ['item' => $item->id]) }}"
                                  data-mark-watched>
                                @csrf
                                @method('PATCH')

                                <button type="submit" aria-label="Mark {{ $item->title }} as watched">
                                    Mark as watched
                                </button>
                                <p role="alert" data-watch-error></p>
                            </form>
                        @endunless
                    </li>
                @endforeach
            </ul>
        @endif
    </main>

    <script>
        document.querySelectorAll('[data-mark-watched]').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const button = form.querySelector('button');
                const status = form.closest('li').querySelector('[data-watch-status]');
                const errorMessage = form.querySelector('[data-watch-error]');

                button.disabled = true;
                errorMessage.textContent = '';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: { Accept: 'application/json' },
                        body: new FormData(form),
                    });

                    if (!response.ok) {
                        throw new Error('Update request failed');
                    }

                    const result = await response.json();

                    if (result.watched !== true) {
                        throw new Error('Unexpected update response');
                    }

                    status.textContent = 'Watched';
                    status.focus();
                    form.remove();
                } catch {
                    errorMessage.textContent = 'Could not confirm the update. Please try again.';
                    button.disabled = false;
                }
            });
        });
    </script>
</body>
</html>