<?php
session_start();

// Default language
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

// Check for language change request
if (isset($_GET['lang'])) {
    $allowed_langs = ['en', 'hi', 'es', 'fr', 'ar']; // We'll add more as needed
    if (in_array($_GET['lang'], $allowed_langs)) {
        $_SESSION['lang'] = $_GET['lang'];
    }
}

// Translation Dictionary
$translations = [
    'en' => [
        'explore' => 'Explore',
        'subscribe' => 'Subscribe',
        'business' => 'SkillStack Business',
        'teach' => 'Teach on SkillStack',
        'login' => 'Log in',
        'signup' => 'Sign up',
        'hero_title' => 'Master New Skills <br> <span class="text-red">With Confidence</span>',
        'hero_subtitle' => 'Learn from industry experts with real-world experience. Join over 2,000+ students starting their careers today.',
        'btn_explore' => 'Explore Courses',
        'btn_teach' => 'Become a Teacher',
        'choose_lang' => 'Choose a language'
    ],
    'hi' => [
        'explore' => 'एक्सप्लोर करें',
        'subscribe' => 'सब्सक्राइब करें',
        'business' => 'स्किलस्टैक बिजनेस',
        'teach' => 'स्किलस्टैक पर पढ़ाएं',
        'login' => 'लॉग इन करें',
        'signup' => 'साइन अप करें',
        'hero_title' => 'नया कौशल सीखें <br> <span class="text-red">पूरे आत्मविश्वास के साथ</span>',
        'hero_subtitle' => 'वास्तविक दुनिया के अनुभव वाले उद्योग विशेषज्ञों से सीखें। आज ही अपना करियर शुरू करने वाले 2,000+ छात्रों में शामिल हों।',
        'btn_explore' => 'कोर्स देखें',
        'btn_teach' => 'शिक्षक बनें',
        'choose_lang' => 'भाषा चुनें'
    ],
    'es' => [
        'explore' => 'Explorar',
        'subscribe' => 'Suscribirse',
        'business' => 'SkillStack Business',
        'teach' => 'Enseña en SkillStack',
        'login' => 'Iniciar sesión',
        'signup' => 'Regístrate',
        'hero_title' => 'Domina nuevas habilidades <br> <span class="text-red">con confianza</span>',
        'hero_subtitle' => 'Aprende de expertos de la industria con experiencia real. Únete a más de 2,000+ estudiantes que comienzan sus carreras hoy.',
        'btn_explore' => 'Explorar cursos',
        'btn_teach' => 'Hazte instructor',
        'choose_lang' => 'Elige un idioma'
    ]
];

// Helper function to get translation
function __($key)
{
    global $translations;
    $lang = $_SESSION['lang'];
    return $translations[$lang][$key] ?? ($translations['en'][$key] ?? $key);
}
?>
