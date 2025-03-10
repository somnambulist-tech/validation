<?php declare(strict_types=1);

return [
    'and' => 'und',
    'or'  => 'oder',

    'rule.default'                  => ':attribute ist ungültig',
    'rule.accepted'                 => ':attribute muss einen der folgenden Werte enthalten: :accepted',
    'rule.after'                    => ':attribute muss nach :time liegen',
    'rule.alpha'                    => ':attribute darf nur Buchstaben enthalten',
    'rule.alpha_dash'               => ':attribute darf nur die folgenden Zeichen enthalten: a-z, 0-9, _ und -',
    'rule.alpha_num'                => ':attribute darf nur Buchstaben und Ziffern enthalten',
    'rule.alpha_spaces'             => ':attribute darf nur Buchstaben und Leerzeichen enthalten',
    'rule.any_of'                   => 'Jeder Wert von :attribute muss einen der folgenden Werte enthalten: :allowed_values',
    'rule.array'                    => ':attribute muss ein Array sein',
    'rule.array_must_have_keys'     => ':attribute müssen alle folgenden Schlüssel angeben: :keys',
    'rule.array_can_only_have_keys' => ':attribute muss nur die folgenden schlüssel haben: :keys',
    'rule.before'                   => ':attribute muss vor :time liegen',
    'rule.between'                  => ':attribute muss zwischen :min und :max liegen',
    'rule.boolean'                  => ':attribute muss vom Typ Boolean sein',
    'rule.date'                     => ':attribute ist kein gültiges Datumsformat',
    'rule.default_value'            => 'Der Standardwert von :attribute ist: :default',
    'rule.different'                => ':attribute muss unterschiedlich zum Feld :field sein',
    'rule.digits'                   => ':attribute darf nur Ziffern enthalten und muss aus genau :length Ziffern bestehen',
    'rule.digits_between'           => ':attribute darf nur Ziffern enthalten und muss zwischen :min und :max liegen',
    'rule.email'                    => ':attribute ist keine gültige E-Mail-Adresse',
    'rule.ends_with'                => ':attribute muss auf :compare_with enden',
    'rule.exists'                   => ':attribute muss einem gültigen Eintrag gleichen',
    'rule.extension'                => ':attribute muss einer der folgenden Dateierweiterungen entsprechen: :allowed_extensions',
    'rule.float'                    => ':attribute muss eine Fließkommazahl sein',
    'rule.in'                       => ':attribute muss einen der folgenden Werte enthalten :allowed_values',
    'rule.integer'                  => ':attribute muss eine ganze Zahl sein',
    'rule.ip'                       => ':attribute muss eine gültige IP-Adresse sein',
    'rule.ipv4'                     => ':attribute muss eine gültige IPv4-Adresse sein',
    'rule.ipv6'                     => ':attribute muss eine gültige IPv6-Adresse sein',
    'rule.json'                     => ':attribute muss eine gültige Zeichenkette im JSON-Format sein',
    'rule.length'                   => ':attribute muss eine Zeichenkette aus genau :length Zeichen sein',
    'rule.lowercase'                => ':attribute darf nur Kleinbuchstaben enthalten',
    'rule.max'                      => 'Der größte erlaubte Wert für :attribute ist :max',
    'rule.mimes'                    => 'Der Dateityp von :attribute muss einer der folgenden sein :allowed_types',
    'rule.min'                      => 'Der kleinste erlaubte Wert von :attribute ist :min',
    'rule.not_in'                   => ':attribute darf keinen der folgenden Werte enthalten: :disallowed_values',
    'rule.numeric'                  => ':attribute muss eine Zahl sein',
    'rule.phone_number'             => ':attribute muss eine gültige Telefonnummer im E.164-Format sein',
    'rule.present'                  => ':attribute muss vorhanden sein',
    'rule.prohibited'               => ':attribute ist nicht erlaubt',
    'rule.prohibited_if'            => ':attribute ist nicht erlaubt, wenn :field einen der folgenden Werte entspricht: :values',
    'rule.prohibited_unless'        => ':attribute ist nicht erlaubt, wenn :field nicht einem der folgenden Werte entspricht: :values',
    'rule.regex'                    => ':attribute entspricht nicht dem erwarteten Format',
    'rule.rejected'                 => ':attribute muss einen der folgenden Werte enthalten: :rejected',
    'rule.required'                 => ':attribute ist erforderlich',
    'rule.requires'                 => ':attribute erfordert :fields',
    'rule.required_if'              => ':attribute ist erforderlich, wenn :field einem der folgenden Werte entspricht: :values',
    'rule.required_unless'          => ':attribute ist erforderlich, außer wenn :field einem der folgenden Werte entspricht :values',
    'rule.required_with'            => ':attribute ist zusammen mit den folgenden Feldern erforderlich: :fields',
    'rule.required_with_all'        => ':attribute ist zusammen mit allen der folgenden Felder erforderlich: :fields',
    'rule.required_without'         => ':attribute ist erforderlich, wenn folgende Feldler leer sind: :fields',
    'rule.required_without_all'     => ':attribute ist erforderlich, wenn alle der folgenden Feldler leer sind: :fields',
    'rule.same'                     => ':attribute muss den gleichen Wert wie :field enthalten',
    'rule.starts_with'              => ':attribute muss mit :compare_with anfangen',
    'rule.string'                   => ':attribute muss vom Typ string sein',
    'rule.unique'                   => ':attribute muss einzigartig sein, der Wert :value existiert jedoch bereits',
    'rule.uploaded_file'            => ':attribute ist keine gültige hochgelandene Datei',
    'rule.uploaded_file.min_size'   => 'Die Datei :attribute ist zu klein, minimale Größe sind :min_size',
    'rule.uploaded_file.max_size'   => 'Die Datei :attribute ist zu groß, maximale Größe sind :max_size',
    'rule.uploaded_file.type'       => ':attribute muss einer der folgenden Dateitypen sein: :allowed_types',
    'rule.uppercase'                => ':attribute darf nur Großbuchstaben enthalten',
    'rule.url'                      => ':attribute muss eine gültige URL sein',
    'rule.uuid'                     => ':attribute ist keine gültige UUID oder ist NULL',
];
