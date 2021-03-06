<?php

// you should copy this to a params.php file and make any modifications 

return [
    'params' => [
        'maxFileSize' => 200000,
        'applications-baseurl' => 'http://app.pdekritis.gr/aitisi',
        'applications-key' => 'your-secret-key',
        'crypt-key-file' => "/path/to/your/key.file",
        // "codes" of specialisations this module *handles*; used to filter specialisations
        'ebp-specialisation-code' => 'ΔΕ1', // this specialisation need special handling, thus set it to locate it in the app
        'applicable-specialisation-codes' => [
            'ΠΕ 2126', // ΘΕΡΑΠΕΥΤΩΝ ΛΟΓΟΥ 
            'ΠΕ 2200', // ΕΠΑΓΓΕΜΑΤΙΚΩΝ ΣΥΜΒΟΥΛΩΝ 
            'ΠΕ 2300', // ΨΥΧΟΛΟΓΩΝ
            'ΠΕ 2400', // ΠΑΙΔΟΨΥΧΙΑΤΡΩΝ 
            'ΠΕ 2500', // ΣΧΟΛΙΚΩΝ ΝΟΣΗΛΕΥΤΩΝ 
            'ΠΕ 2800', // ΦΥΣΙΚΟΘΕΡΑΠΕΥΤΩΝ 
            'ΠΕ 2900', // ΕΡΓΑΣΙΟΘΕΡΑΠΕΥΤΩΝ 
            'ΠΕ 3000', // ΚΟΙΝΩΝΙΚΩΝ ΛΕΙΤΟΥΡΓΩΝ 
            'ΠΕ 3100', // ΕΞΕΙΔΙΚΕΥΜΕΝΟΥΣ
            'ΔΕ1' // ΕΒΠ 
        ],
        'extra-call-teachers-percent' => 0.2, // call an extra 20% of teachers to cover loses; ONLY USED WHEN call does not explicitely define number of teachers to call 
    ]
];
