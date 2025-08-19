<?php

$token = "8404636086:AAFPh2-VDFsW9LcV7qCCrKODo3ehteioQmI"; // حط التوكن بتاعك

$input = file_get_contents("php://input");
$update = json_decode($input, true);

$chat_id = $update["message"]["chat"]["id"];
$text = $update["message"]["text"];

file_put_contents("users/$chat_id.txt", $text); // عشان نقدر نتابع الخطوات

// التحقق من الخطوة الحالية
$step = trim(@file_get_contents("users/$chat_id.step"));

if ($text == "/start") {
    $welcome = "🤖 أهلاً بك في بوت الظلام يا ديمون 😈\n\n" .
               "بوت من برمجة تيم DX 💀\n\n" .
               "اختر أداتك من الأسلحة الإلكترونية أدناه 👇";

    $keyboard = [
        "keyboard" => [
            [["text" => "🌍 جلب معلومات IP"], ["text" => "📲 جلب معلومات رقم"]],
            [["text" => "🔎 جلب معلومات تيلي من يوزر"], ["text" => "💣 تعليم الهكر"]]
        ],
        "resize_keyboard" => true
    ];

    sendMessage($chat_id, $welcome, $keyboard);
    file_put_contents("users/$chat_id.step", "none");

} elseif ($text == "🌍 جلب معلومات IP") {
    sendMessage($chat_id, "أرسل الـ IP الآن 🔍");
    file_put_contents("users/$chat_id.step", "ip");

} elseif ($step == "ip") {
    // تنفيذ جلب معلومات IP
    $ip = trim($text);
    $api = "http://ip-api.com/json/$ip?fields=status,country,regionName,city,query,isp,org";
    $data = json_decode(file_get_contents($api), true);

    if ($data['status'] == 'success') {
        $info = "📡 معلومات عن الـ IP:\n";
        $info .= "🌍 الدولة: " . $data['country'] . "\n";
        $info .= "📍 المدينة: " . $data['city'] . "\n";
        $info .= "🏢 الشركة: " . $data['org'] . "\n";
        $info .= "🧠 مزود الخدمة: " . $data['isp'] . "\n";
        $info .= "🔢 IP: " . $data['query'];
    } else {
        $info = "❌ لم يتم العثور على معلومات لهذا الـ IP.";
    }

    sendMessage($chat_id, $info);
    file_put_contents("users/$chat_id.step", "none");

} elseif ($text == "📲 جلب معلومات رقم") {
    sendMessage($chat_id, "أرسل الرقم مع كود الدولة مثل: +201234567890 ☎️");
    file_put_contents("users/$chat_id.step", "number");

} elseif ($step == "number") {
    // هنا ممكن ربطه مع API خارجي مثل numverify أو truecaller (محتاج API Key)
    $number = $text;
    sendMessage($chat_id, "🔍 تم استلام الرقم: $number\n(⚠️ الميزة قيد التطوير حالياً من فريق DX)");
    file_put_contents("users/$chat_id.step", "none");

} elseif ($text == "🔎 جلب معلومات تيلي من يوزر") {
    sendMessage($chat_id, "أرسل اليوزر بالشكل: @username 👁️");
    file_put_contents("users/$chat_id.step", "telegram");

} elseif ($step == "telegram") {
    $username = trim($text);
    sendMessage($chat_id, "❌ المعذرة يا ديمون، تيليجرام مش بيدي معلومات المستخدمين مباشرة عبر اليوزر لحماية الخصوصية 🔒\nلكن فيه أدوات خارجة عن TOS ممكن نكلم عليها برا 😏");
    file_put_contents("users/$chat_id.step", "none");

} elseif ($text == "💣 تعليم الهكر") {
    sendMessage($chat_id, "📚 بدأنا يا هاكر الظلام!\nهنتعلم Social Engineering - Phishing - WiFi Hacks والمزيد...\nتابعني وهجيلك الدروس تباعًا 😎💀");
    file_put_contents("users/$chat_id.step", "none");

} else {
    sendMessage($chat_id, "❓ مش فاهم عليك، اختَر من الأزرار.");
}

// دالة إرسال الرسالة
function sendMessage($chat_id, $message, $keyboard = null) {
    global $token;
    $url = "https://api.telegram.org/bot$token/sendMessage";

    $post = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

    if ($keyboard) {
        $post['reply_markup'] = json_encode($keyboard);
    }

    file_get_contents($url . "?" . http_build_query($post));
}
