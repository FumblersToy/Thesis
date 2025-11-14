<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bandmate | Business Partnership</title>
    @vite(['resources/css/create-business.css', 'resources/js/create-business.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.min.js"></script>
</head>
<body>
    <div class="form-container">
        <div class="header">
            <h1>Create Business Profile</h1>
            <p>Connect your venue with talented musicians</p>
        </div>

        <form enctype="multipart/form-data" method="POST" action="{{ route('business.store') }}">
            @csrf
            <!-- Profile Image Upload -->
            <div class="form-group">
                <label for="profile_image">Business Logo</label>
                <div class="file-input-wrapper">
                    <input type="file" name="profile_picture" id="profile_image" accept="image/*" class="file-input">
                    <label for="profile_image" class="file-input-label" id="file-label">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                            <circle cx="12" cy="13" r="3"/>
                        </svg>
                        <span id="file-text">Choose your logo</span>
                    </label>
                </div>
            </div>

            <!-- Business Name -->
            <div class="form-group">
                <label for="business_name">Establishment Name</label>
                <input type="text" id="business_name" name="business_name" required placeholder="Enter your business name">
            </div>

            <!-- Venue Offered -->
            <div class="form-group">
                <label for="venue">Venue Offered</label>
                <select id="venue" name="venue" required>
                    <option value="" disabled selected>Select the venue you offer</option>
                    <option value="Studio">Studio</option>
                    <option value="Club">Club</option>
                    <option value="Theater">Theater</option>
                    <option value="Cafe">Café</option>
                    <option value="Restaurant">Restaurant</option>
                    <option value="Bar">Bar & Lounge</option>
                    <option value="Event Venue">Event Venue</option>
                    <option value="Music Hall">Music Hall</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <select name="location" id="location" required>
                    <option value="" disabled selected>Select your location</option>
                    <option value="Balibago">Balibago</option>
                    <option value="CM Recto">CM Recto</option>
                    <option value="Clark">Clark</option>
                    <option value="Malabanias">Malabanias</option>
                    <option value="Friendship">Friendship</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <!-- Contact Information Row -->
            <div class="form-row">
                <div class="form-group has-icon">
                    <label for="email">Business Email</label>
                    <input type="email" id="email" name="contact_email" required placeholder="contact@yourbusiness.com">
                    <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
                <div class="form-group phone-input-wrapper">
                    <label for="phone">Phone Number</label>
                    <div class="phone-prefix">+63</div>
                    <input type="tel" id="phone" name="phone_number" class="phone-input" 
                        maxlength="13" required
                        placeholder="917 123 4567">
                </div>
            </div>

            <!-- Business Address -->
            <div class="form-group has-icon">
                <label for="business_address">Business Address</label>
                <input type="text" id="business_address" name="address" required 
                    placeholder="Complete address including city">
                <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
            </div>

            <button type="submit" class="submit-btn">
                Create Profile
            </button>
        </form>
    </div>
</body>
</html>