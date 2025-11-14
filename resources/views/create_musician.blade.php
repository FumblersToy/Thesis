<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bandmate | Create Your Profile</title>
    @vite(['resources/css/app.css', 'resources/css/create-musician.css', 'resources/js/create-musician.js', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.min.js"></script>
</head>
<body>
    <div class="form-container">
        <div class="header">
            <h1 class="text-2xl font-bold">Create Your Profile</h1>
            <p>Let's get you connected with fellow musicians</p>
        </div>

        <form enctype="multipart/form-data" method="POST" action="{{ route('musician.store') }}">
            @csrf
            <!-- Profile Image Upload -->
            <div class="form-group">
                <label for="profile_picture">Profile Picture</label>
                <div class="file-input-wrapper">
                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="file-input">
                    <label for="profile_picture" class="file-input-label" id="file-label">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21,15 16,10 5,21"/>
                        </svg>
                        <span id="file-text">Choose a photo</span>
                    </label>
                </div>
            </div>

            <!-- Name Fields -->
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required placeholder="Enter your first name">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required placeholder="Enter your last name">
                </div>
            </div>

            <!-- Stage Name -->
            <div class="form-group">
                <label for="stage_name">Stage Name</label>
                <input type="text" id="stage_name" name="stage_name" required placeholder="How do you want to be known?">
            </div>

            <!-- Genre -->
            <div class="form-group">
                <label for="genre">Primary Genre</label>
                <select id="genre" name="genre" required>
                    <option value="" disabled selected>Choose your main genre</option>
                    <option value="RnB">RnB</option>
                    <option value="House">House</option>
                    <option value="Pop Punk">Pop Punk</option>
                    <option value="Electronic">Electronic</option>
                    <option value="Reggae">Reggae</option>
                    <option value="Jazz">Jazz</option>
                    <option value="Rock">Rock</option>
                </select>
            </div>

            <!-- Instrument -->
            <div class="form-group">
                <label for="instrument">Primary Instrument</label>
                <select id="instrument" name="instrument">
                    <option value="" disabled selected>Choose your main instrument</option>
                    <option value="Guitar">Guitar</option>
                    <option value="Drums">Drums</option>
                    <option value="Piano">Piano</option>
                    <option value="Bass">Bass</option>
                    <option value="Vocals">Vocals</option>
                    <option value="Violin">Violin</option>
                    <option value="Saxophone">Saxophone</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
            label for="location">Location</label>
                <select name="location" id="location">
                    <option value="" disabled selected>Select your location</option>
                    <option value="Balibago">Balibago</option>
                    <option value="CM Recto">CM Recto</option></option>
                    <option value="Pampang">Pampang</option>
                    <option value="San Nicolas">San Nicolas</option>
                    <option value="Santa Teresa">Santa Teresa</option>
                    <option value="Anunas">Anunas</option>
                    <option value="Agapito del Rosario">Agapito del Rosario</option>
                    <option value="Cutcut">Cutcut</option>
                    <option value="Capaya">Capaya</option>
                    <option value="Telabastagan">Telabastagan</option>
                    <option value="Lourdes">Lourdes</option>
                    <option value="Malabanias">Malabanias</option>
                    <option value="Tabun">Tabun</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <!-- Bio -->
            <div class="form-group">
                <label for="bio">Tell Your Story</label>
                <textarea 
                    id="bio" 
                    name="bio" 
                    maxlength="500" 
                    placeholder="Share your musical journey, influences, what drives your passion for music..."
                ></textarea>
                <div class="char-counter">
                    <span id="char-count">0</span>/500 characters
                </div>
            </div>

            <button type="submit" class="submit-btn">
                Create My Profile
            </button>
        </form>
    </div>
</body>
</html>