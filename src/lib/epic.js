import { jwtDecode } from "jwt-decode";

export const ClientID = "xyza7891RMFYAPCpkI4fX4GKL5U7j1jC";
export const DeploymentID = "c4ccbce82d1443b99bd31b25c0385dac";

export function createOauthUrl(redirect_to = "https://oblivion-esport.fr/v2/login/epic") {
    return `https://www.epicgames.com/id/authorize?client_id=${ClientID}&redirect_uri=${redirect_to}&response_type=code&scope=basic_profile `;
}

export async function getAccessToken(code) {
    const response = await fetch("https://api.epicgames.dev/epic/oauth/v2/token", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `grant_type=authorization_code&code=${code}&deployment_id=${DeploymentID}&scope=basic_profile`,
    });
    return response.json();
}

export function getUserInfo(accessToken) {
    // get user display name directly from the JWT token
    const decodedToken = jwtDecode(accessToken);
    const displayName = decodedToken.dn;
    return { displayName };

}