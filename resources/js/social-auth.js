// resources/js/auth/social-auth.js

export class SocialAuth {
    constructor() {
        // Use import.meta.env instead of process.env for Vite
        this.googleClientId = import.meta.env.VITE_GOOGLE_CLIENT_ID;
        this.facebookAppId = import.meta.env.VITE_FACEBOOK_APP_ID;
        this.initializeGoogleAuth();
        this.initializeFacebookAuth();
    }

    async initializeGoogleAuth() {
        try {
            await this.loadGoogleScript();

            return new Promise((resolve) => {
                window.gapi.load('auth2', async () => {
                    try {
                        const auth2 = await window.gapi.auth2.init({
                            client_id: this.googleClientId
                        });
                        resolve(auth2);
                    } catch (error) {
                        console.error('Google Auth initialization failed:', error);
                    }
                });
            });
        } catch (error) {
            console.error('Failed to load Google script:', error);
        }
    }

    loadGoogleScript() {
        return new Promise((resolve, reject) => {
            if (document.querySelector('script[src*="apis.google.com"]')) {
                resolve();
                return;
            }
            const script = document.createElement('script');
            script.src = 'https://apis.google.com/js/api:client.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    async handleGoogleLogin() {
        try {
            const auth2 = await window.gapi.auth2.getAuthInstance();
            if (!auth2) {
                await this.initializeGoogleAuth();
            }

            const googleUser = await window.gapi.auth2.getAuthInstance().signIn();
            const token = googleUser.getAuthResponse().id_token;

            const response = await fetch('/api/auth/google/callback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ token }),
                credentials: 'include'
            });

            const data = await response.json();
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                throw new Error(data.error || 'Authentication failed');
            }
        } catch (error) {
            console.error('Google login failed:', error);
            // Handle error (show error message to user)
        }
    }

    initializeFacebookAuth() {
        window.fbAsyncInit = () => {
            FB.init({
                appId: this.facebookAppId,
                cookie: true,
                xfbml: true,
                version: 'v18.0'
            });
        };

        // Load Facebook SDK
        (function (d, s, id) {
            if (d.getElementById(id)) return;
            const js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            const fjs = d.getElementsByTagName(s)[0];
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    }

    async handleFacebookLogin() {
        try {
            const response = await new Promise((resolve, reject) => {
                FB.login((response) => {
                    if (response.authResponse) {
                        resolve(response);
                    } else {
                        reject(new Error('User cancelled login or did not fully authorize.'));
                    }
                }, { scope: 'email,public_profile' });
            });

            const { accessToken } = response.authResponse;

            const apiResponse = await fetch('/api/auth/facebook/callback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ token: accessToken }),
                credentials: 'include'
            });

            const data = await apiResponse.json();
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                throw new Error(data.error || 'Authentication failed');
            }
        } catch (error) {
            console.error('Facebook login failed:', error);
            // Handle error (show error message to user)
        }
    }
}