import { jwtDecode } from "jwt-decode";
import { supabase } from "$lib/supabaseClient";

export const ClientID = "3ba9b93b-8c9e-4e5d-a164-95c8fadf2a99";


export function createOauthUrl(redirect_to = "https://oblivion-esport.fr/login/riot") {
    // https://auth.riotgames.com/authorize?client_id={your-client-id}&redirect_uri={your-redirect-uri}&response_type=code&scope=openid+offline_access.
    return `https://auth.riotgames.com/authorize?client_id=${ClientID}&redirect_uri=${redirect_to}&response_type=code&scope=openid`;
}

export async function getAccessToken(code) {
    const { data, error } = await supabase.functions.invoke('auth-riot', {
        body: { authorization_code: code },
    })
    console.log(data, error);
    return data;
}

export function getName(accessToken) {
    // get user display name directly from the JWT token
    const decodedToken = jwtDecode(accessToken);
    console.log(decodedToken);
    const displayName = decodedToken.dn;
    return displayName;
}