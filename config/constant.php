<?php

return [

    'email_varified'    => false,
    'secret_token'      => 'k9l3xJuL6D9dBmvPIDMe6Th3Wj8WpzeJKvDbcBU4vgsdfgvdgdfN6DOVXmZzgKHEZ2hPYdGsyhhJdmCWzvFkGpl',
    'phoneRegExp'       => "/^(?:(?:\+|0{0,2})91(\s*|[-])?|[0]?)?([6789]\d{2}([ -]?)\d{3}([ -]?)\d{4})$/",
    'emailRegExp'       => '/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i',
    'gstinRegExp'       => "/\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z\d]{1}[Z]{1}[A-Z\d]{1}/",

    'setting_array'     => [
        '1'             => 'General Settings',
        '2'             => 'Social Links Setting',
        '3'             => 'Mail Setting',
        '5'             => 'Whatsapp & SMS Setting',
        '8'             => 'Google Recaptcha',
        '9'             => 'Counters',
        '10'            => 'Razor Pay',
    ],

    'claimant_address_type' => [
        '1' => 'Home', 
        '2' => 'Office' 
    ],

    'respondent_address_type' => [
        '1' => 'Home', 
        '2' => 'Office' 
    ],

    'drp_type' => [
        '1' => 'Arbitrator', 
        '2' => 'Advocate',
        '3' => 'Case Manager',
        '4' => 'Mediator',
        '5' => 'Conciliator'
    ],

    'case_type' => [
        '1' => 'ARBITRATION', 
        '2' => 'MEDIATION',
        '3' => 'CONCILIATION',
        '4' => 'CIVIL',
        '5' => 'CRIMINAL',
        '6' => 'NI ACT',
        '7' => 'PASA ACT',
        '8' => 'CONSUMER',
        '9' => 'LOK ADALAT',
        '10' => 'OTHER',
    ],
    
    'product_type' => [
        '1' => 'TW', 
        '2' => 'AUTO',
        '3' => 'UCL',
        '4' => 'CV',
        '5' => 'CE',
        '6' => 'HL',
        '7' => 'LAP',
        '8' => 'PL',
        '9' => 'BL',
        '10' => 'CC',
        '11' => 'OTHER',
    ],

    'language' => [
        '1' => 'Hindi', 
        '2' => 'English' 
    ],

    'stage_type' => [
        '1' => 'Stage 1-A Notice: Intimation of Appointment of MediateWay ADR Centre as Institutional Arb.', 
        '2' => 'Stage 1-A Msg.: Intimation of Appointment of MediateWay ADR Centre as Institutional Arb.',
        '3' => 'Stage 1-A Mail ID.: Intimation of Appointment of MediateWay ADR Centre as Institutional Arb.',
        '4' => 'Stage 2-Notice: The intimation about case registration',
        '5' => 'Stage 2-MSG: The intimation about case registration',
        '6' => 'Stage 2-Mail ID: The intimation about case registration',
        '7' => 'Stage 3-A Notice: PROPOSAL LETTER FOR Appointment of Arbitrator',
        '8' => 'Stage 3-A Msg: PROPOSAL LETTER FOR Appointment of Arbitrator',
        '9' => 'Stage 3-A Mail: PROPOSAL LETTER FOR Appointment of Arbitrator',
        '10' => 'Stage 3-B Notice: Appointment of Arbitrator',
        '11' => 'Stage 3-B Msg.: Appointment of Arbitrator',
        '12' => 'Stage 3-B Mail : Appointment of Arbitrator',
        '13' => 'Stage 3-C Notice: Acceptance and Disclosure',
        '14' => 'Stage 3-C Msg: Acceptance and Disclosure',
        '15' => 'Stage 3-C Mail ID: Acceptance and Disclosure',
        '16' => 'Stage 4-Notice: Challenge the Arbitrator’s Appointment',
        '17' => 'Stage 4-Msg.: Challenge the Arbitrator’s Appointment',
        '18' => 'Stage 4-Mail.: Challenge the Arbitrator’s Appointment',
        '19' => 'Stage 4-Mail: Challenge the Arbitrator’s Appointment',
        '20' => 'Stage 5-A Notice: Arbitrator 1st Notice',
        '21' => 'Stage 5-A Msg: Arbitrator 1st Notice',
        '22' => 'Stage 5-A Mail: Arbitrator 1st Notice',
        '23' => 'Stage 5-B Notice: Final Notice for Granting Time to Upload the Claim',
        '24' => 'Stage 5-B Msg: Final Notice for Granting Time to Upload the Claim',
        '25' => 'Stage 5-B Mail: Final Notice for Granting Time to Upload the Claim',
        '26' => 'Stage 6-A Notice: Arbitrator 2nd Notice',
        '27' => 'Stage 6-A Msg.: Arbitrator 2nd Notice',
        '28' => 'Stage 6-A Mail: Arbitrator 2nd Notice',
        '29' => 'Stage 6-B Notice: Arbitrator 3rd Notice for Sought for Adjournment',
        '30' => 'Stage 6-B Msg.: Arbitrator 3rd Notice for Sought for Adjournment',
        '31' => 'Stage 6-B Mail: Arbitrator 3rd Notice for Sought for Adjournment',
        '32' => 'Stage 6-BB Notice: Arbitrator 3rd Notice for Absent',
        '33' => 'Stage 6-BB Msg.: Arbitrator 3rd Notice for Absent',
        '34' => 'Stage 6-BB Mail: Arbitrator 3rd Notice for Absent',
        '35' => 'Stage 6-C-1 Notice: For Rejoinder of the Respondent’s Reply or Counterclaim',
        '36' => 'Stage 6-C-1 Msg.: For Rejoinder of the Respondent’s Reply or Counterclaim',
        '37' => 'Stage 6-C-1 Mail: For Rejoinder of the Respondent’s Reply or Counterclaim',
        '38' => 'Stage 8-Intimation of Reserv of Award Msg.',
        '39' => 'Stage 8-Intimation of Reserv of Award Mail',
        '40' => 'Stage 9-Passing of Award  & Post',
        '41' => 'Stage 9-Passing of Award-Msg.',
        '42' => 'Stage 9-Passing of Award-Mail',
    ],
    

    'notice_type' => [
        '1' => 'Stage 1 Notice', 
        '2' => 'Stage 1A Notice',
        '3' => 'Stage 1B Notice',
        '4' => 'Stage 2A Notice',
        '5' => 'Stage 3A Notice',
        '6' => 'Stage 3B Notice',
        '7' => 'Stage 3C Notice',
        '8' => 'Stage 3D Notice',
        '9' => 'Stage 4A Notice',
        '10' => 'Stage 5A Notice',
        '11' => '4A OrderSheet',
        '12' => '5A OrderSheet',
    ],


    'organization_type' => [
        '1' => 'Bank', 
        '2' => 'NBFC',
        '3' => 'Limited Company',
        '4' => 'Society',
        '5' => 'Others',
    ],

];
