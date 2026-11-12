function createCinemaShowtimes(film, endpoints) {
    const panel = document.createElement('details');
    const summary = document.createElement('summary');
    summary.textContent = 'Find cinema showtimes';
    summary.setAttribute('aria-label', `Find cinema showtimes for ${film.title}`);

    const dateLabel = document.createElement('label');
    const dateInput = document.createElement('input');
    const today = new Intl.DateTimeFormat('en-CA', {
        timeZone: 'Europe/London',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    }).formatToParts(new Date());
    const dateParts = Object.fromEntries(today.map(({ type, value }) => [type, value]));
    dateInput.type = 'date';
    dateInput.min = `${dateParts.year}-${dateParts.month}-${dateParts.day}`;
    dateInput.value = dateInput.min;
    dateInput.required = true;
    dateLabel.append('Screening date ', dateInput);

    const locationButton = document.createElement('button');
    locationButton.type = 'button';
    locationButton.textContent = 'Use my location';

    const birminghamButton = document.createElement('button');
    birminghamButton.type = 'button';
    birminghamButton.className = 'secondary';
    birminghamButton.textContent = 'Use Birmingham city centre';

    const editionLabel = document.createElement('label');
    const editionSelect = document.createElement('select');
    editionLabel.append('Cinema edition ', editionSelect);

    const showtimesButton = document.createElement('button');
    showtimesButton.type = 'button';
    showtimesButton.textContent = 'Check screenings';

    const editionControls = document.createElement('div');
    editionControls.hidden = true;
    editionControls.append(editionLabel, ' ', showtimesButton);

    const status = document.createElement('p');
    status.setAttribute('role', 'status');

    const results = document.createElement('ul');
    results.className = 'film-list';
    results.setAttribute('role', 'list');

    const locationNote = document.createElement('p');
    locationNote.textContent = 'If you use your location, approximate coordinates are sent to MovieGlu to find nearby screenings.';

    panel.append(
        summary,
        locationNote,
        dateLabel,
        document.createElement('p'),
        locationButton,
        ' ',
        birminghamButton,
        status,
        editionControls,
        results,
    );

    let location = null;

    function setBusy(busy) {
        locationButton.disabled = busy;
        birminghamButton.disabled = busy;
        showtimesButton.disabled = busy;
        editionSelect.disabled = busy;
        dateInput.disabled = busy;
    }

    async function getJson(endpoint, parameters) {
        const url = new URL(endpoint, window.location.origin);

        for (const [name, value] of Object.entries(parameters)) {
            url.searchParams.set(name, value);
        }

        const response = await fetch(url, {
            headers: { Accept: 'application/json' },
            cache: 'no-store',
        });

        if (!response.ok) {
            throw new Error('Cinema request failed');
        }

        return response.json();
    }

    async function loadEditions(latitude, longitude, label) {
        setBusy(true);
        location = null;
        editionControls.hidden = true;
        editionSelect.replaceChildren();
        results.replaceChildren();
        status.textContent = `Looking for cinema editions near ${label}…`;

        const coordinates = {
            latitude: latitude.toFixed(3),
            longitude: longitude.toFixed(3),
        };

        try {
            const { editions } = await getJson(endpoints.editions, coordinates);

            if (editions.length === 0) {
                status.textContent = 'MovieGlu returned no matching cinema editions for this film.';
                return;
            }

            for (const edition of editions) {
                const option = document.createElement('option');
                option.value = String(edition.id);
                option.textContent = edition.name;
                editionSelect.append(option);
            }

            location = { coordinates, label };
            editionControls.hidden = false;
            status.textContent = `Searching from ${label}. Choose an edition and check screenings.`;
        } catch {
            status.textContent = 'Could not load cinema editions. Please try again.';
        } finally {
            setBusy(false);
        }
    }

    locationButton.addEventListener('click', () => {
        if (!navigator.geolocation) {
            status.textContent = 'Location is unavailable. You can use Birmingham city centre.';
            return;
        }

        setBusy(true);
        location = null;
        editionControls.hidden = true;
        results.replaceChildren();
        status.textContent = 'Finding your location…';

        navigator.geolocation.getCurrentPosition(
            ({ coords }) => loadEditions(coords.latitude, coords.longitude, 'your location'),
            () => {
                status.textContent = 'Could not get your location. You can use Birmingham city centre.';
                setBusy(false);
            },
            { timeout: 10000 },
        );
    });

    birminghamButton.addEventListener('click', () => {
        loadEditions(52.486, -1.890, 'Birmingham city centre');
    });

    dateInput.addEventListener('change', clearScreenings);
    editionSelect.addEventListener('change', clearScreenings);

    function clearScreenings() {
        results.replaceChildren();

        if (location) {
            status.textContent = 'Click Check screenings to see results for your selection.';
        }
    }

    showtimesButton.addEventListener('click', async () => {
        if (!location || !dateInput.reportValidity()) {
            return;
        }

        const date = dateInput.value;
        const editionId = editionSelect.value;

        setBusy(true);
        results.replaceChildren();
        status.textContent = `Checking screenings for ${date}…`;

        try {
            const { cinemas } = await getJson(endpoints.showtimes(editionId), {
                ...location.coordinates,
                date,
            });

            for (const cinema of cinemas) {
                const row = document.createElement('li');
                row.className = 'film-row';

                const name = document.createElement('strong');
                name.textContent = cinema.cinema_name;
                row.append(name);

                if (typeof cinema.distance === 'number') {
                    row.append(` ${cinema.distance} miles away`);
                }

                const screenings = document.createElement('ul');

                for (const [format, showing] of Object.entries(cinema.showings ?? {})) {
                    const times = (showing.times ?? []).map((time) => time.start_time);

                    if (times.length > 0) {
                        const line = document.createElement('li');
                        line.textContent = `${format}: ${times.join(', ')}`;
                        screenings.append(line);
                    }
                }

                if (screenings.childElementCount > 0) {
                    row.append(screenings);
                    results.append(row);
                }
            }

            const count = results.childElementCount;
            status.textContent = count === 0
                ? `No screenings listed for ${date} near ${location.label}. Try another date or edition.`
                : `${count} ${count === 1 ? 'cinema' : 'cinemas'} with screenings on ${date}. Showtimes from MovieGlu.`;
        } catch {
            results.replaceChildren();
            status.textContent = 'Could not load cinema showtimes. Please try again.';
        } finally {
            setBusy(false);
        }
    });

    return panel;
}