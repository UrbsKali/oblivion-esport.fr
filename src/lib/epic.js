import { jwtDecode } from "jwt-decode";

export const ClientID = "xyza7891RMFYAPCpkI4fX4GKL5U7j1jC";
export const DeploymentID = "c4ccbce82d1443b99bd31b25c0385dac";

const secret = "oF8Hz9pNp9fDQoaYarAe";

export function createOauthUrl(redirect_to = "https://oblivion-esport.fr/v2/login/epic") {
    return `https://www.epicgames.com/id/authorize?client_id=${ClientID}&redirect_uri=${redirect_to}&response_type=code&scope=basic_profile `;
}

export async function getAccessToken(code) {
    const response = await fetch(`https://oblivion-esport.fr/api/riot.php?authorization_code=${code}?secret=${secret}`);
    return response.json();
}

export function getName(accessToken) {
    // get user display name directly from the JWT token
    const decodedToken = jwtDecode(accessToken);
    const displayName = decodedToken.dn;
    return displayName;

}