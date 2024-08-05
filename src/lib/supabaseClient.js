
import { createClient } from "@supabase/supabase-js";

const supabaseUrl = "https://idlcqblimgotmibuednf.supabase.co";
const supabaseKey = `eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImlkbGNxYmxpbWdvdG1pYnVlZG5mIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MjI4NjA2MTUsImV4cCI6MjAzODQzNjYxNX0.oaBFc5Gx6p2BgqovzPvZ_yXGR7h_wV8dg0FkjJlrRx0`;

// @ts-ignore
export const supabase = createClient(supabaseUrl, supabaseKey);
