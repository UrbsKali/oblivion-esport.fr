import { supabase } from "$lib/supabaseClient";

export const ClientID = "3ba9b93b-8c9e-4e5d-a164-95c8fadf2a99";


export function createOauthUrl(redirect_to = "https://oblivion-esport.fr/login/riot") {
    // https://auth.riotgames.com/authorize?client_id={your-client-id}&redirect_uri={your-redirect-uri}&response_type=code&scope=openid+offline_access.
    return `https://auth.riotgames.com/authorize?client_id=${ClientID}&redirect_uri=${redirect_to}&response_type=code&scope=openid+offline_access`;
}

export async function getAccessToken(code) {
    const { data, error } = await supabase.functions.invoke('auth-riot', {
        body: { authorization_code: code },
    })
    console.log(data, error);
    return data;
}

export async function getName(accessToken) {
    // get display name from the /userinfo endpoint
    const url = "https://auth.riotgames.com/userinfo";
    const headers = {
        Authorization: `Bearer ${accessToken}`,
    };
    const response = fetch(url, { headers });
    const data = await response.json();
    console.log(data);
    return data.sub;
}