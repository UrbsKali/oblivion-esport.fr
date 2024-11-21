import { userdata } from '$lib/store';
import { supabase } from '$lib/supabaseClient';
import md5 from 'crypto-js/md5';

export async function loadUserdata() {
    let user = {};
    const {
        data: { session },
        error
    } = await supabase.auth.getSession();
    if (error) {
        console.error(error);
        return;
    }
    if (session) {
        // fetch user data
        const { data, error } = await supabase
            .from('profiles')
            .select('username,avatar_url, tracker, perms!inner(permissions)')
            .eq('id', session.user.id)
            .single();
        if (error) {
            console.error(error);
            return;
        }
        if (data.avatar_url == "") {
            data.avatar_url = "https://gravatar.com/avatar/" + md5(session.user.email) + "?d=identicon";
        }
        user.email = session.user.email || user.email;
        user.name = data.username || user.email.split('@')[0];
        user.avatar = data.avatar_url || user.avatar;
        user.id = session.user.id;
        user.tracker = data.tracker || user.tracker;

        if (data.perms.permissions) {
            user.permissions = data.perms.permissions;
        }


        userdata.set(user);

        {
            const { data: dat, error } = await supabase.auth.getUserIdentities();
            if (error) {
                console.error(error);
                return;
            }

            let providers = dat;

            const { data, error: err } = await supabase.from('other_providers').select('provider, info, display_name').eq('user_id', session.user.id);
            if (err) {
                console.error(err);
                return;
            }

            if (data) {
                // check for overlap
                data.forEach((element) => {
                    let i = providers.identities.findIndex((el) => el.provider === element.provider);
                    if (i !== -1) {
                        providers.identities.splice(i);
                        element.save = true;
                    }
                });


                user.providers = [...dat.identities, ...data];
                userdata.set(user);
            }
        }

    }
}

export function loadSettings(key) {
    let settings_;
    try {
        settings_ = JSON.parse(window.localStorage.getItem(`settings_${key}`)) || [];
    } catch (e) {
        console.error("echec lors de la récupération des données, la fonction est problement executé depuis le serveur")
        return;
    }
    return settings_
}

export function saveSettings(key, settings) {
    try {
        localStorage.setItem(`settings_${key}`, JSON.stringify(settings));
    } catch (e) {
        console.error("echec lors de l'enregistrement, la fonction est problement executé depuis le serveur")
        return;
    }
}

export function formatText(text) {
    // to text
    if (text === null || text === undefined) {
        return '';
    }
    if (typeof text !== 'string') {
        text = text.toString();
    }
    if (text?.length > 50) {
        return text.slice(0, 50) + '...';
    }
    return text;
}

export function hashCode(obj) {
    let str = JSON.stringify(obj);
    let hash = 0;
    for (let i = 0, len = str.length; i < len; i++) {
        let chr = str.charCodeAt(i);
        hash = (hash << 5) - hash + chr;
        hash |= 0; // Convert to 32bit integer
    }
    return hash;
}

export function hideOnClickOutside(element, destroyHandler = (el) => {
    el.classList.toggle("hidden")
}) {
    const outsideClickListener = event => {
        if (!element.contains(event.target) && isVisible(element)) { // or use: event.target.closest(selector) === null
            destroyHandler(element);
            removeClickListener();
        }
    }

    const removeClickListener = () => {
        document.removeEventListener('click', outsideClickListener);
    }

    document.addEventListener('click', outsideClickListener);
}
const isVisible = elem => !!elem && !!(elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length); // source (2018-03-11): https://github.com/jquery/jquery/blob/master/src/css/hiddenVisibleSelectors.js 