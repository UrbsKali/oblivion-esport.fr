import { jwtDecode } from "jwt-decode";
import { supabase } from "$lib/supabaseClient";

export const ClientID = "xyza7891RMFYAPCpkI4fX4GKL5U7j1jC";
export const DeploymentID = "c4ccbce82d1443b99bd31b25c0385dac";


export function createOauthUrl(redirect_to = "https://oblivion-esport.fr/login/epic") {
    return `https://www.epicgames.com/id/authorize?client_id=${ClientID}&redirect_uri=${redirect_to}&response_type=code&scope=basic_profile `;
}

export async function getAccessToken(code) {
    const { data, error } = await supabase.functions.invoke('auth-epic', {
        body: { authorization_code: code },
    })
    return data;
}

export function getName(accessToken) {
    // get user display name directly from the JWT token
    const decodedToken = jwtDecode(accessToken);
    const displayName = decodedToken?.dn;
    return displayName;
}