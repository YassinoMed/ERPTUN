<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Utility;
use App\Models\Invoice;
use App\Models\Customer;
use Orhanerday\OpenAi\OpenAi;

class AiCopilotController extends Controller
{
    public function ask(Request $request)
    {
        $message = $request->input('message');
        
        $settings = Utility::settings();
        $open_ai_key = env('OPENAI_API_KEY') ?? ($settings['chatgpt_key'] ?? '');

        if (empty($open_ai_key)) {
            return response()->json(['reply' => "La clé API OpenAI n'est pas configurée dans les paramètres de l'ERP ou le fichier .env."]);
        }

        $open_ai = new OpenAi($open_ai_key);

        $prompt = "Tu es Copilot, l'assistant IA de l'ERP SaaS Odoo-like.
Réponds aux questions de l'utilisateur de manière concise et utile. L'utilisateur actuel s'appelle " . Auth::user()->name . ".
Si l'utilisateur demande des informations sur les factures impayées, dis-lui que tu peux chercher ces informations si tu y as accès.
La requête : " . $message;

        $chat = $open_ai->chat([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    "role" => "system",
                    "content" => $prompt
                ],
            ],
            'temperature' => 0.7,
            'max_tokens' => 500,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);

        $d = json_decode($chat);
        
        if (isset($d->error)) {
            return response()->json(['reply' => "Erreur OpenAI: " . $d->error->message]);
        }

        $reply = $d->choices[0]->message->content ?? "Je n'ai pas pu comprendre votre demande.";

        return response()->json(['reply' => $reply]);
    }
}
