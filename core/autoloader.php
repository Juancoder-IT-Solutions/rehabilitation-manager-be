<?php

$pre_class = "classes/class.";
$classes = array(
    'Users'                 => $pre_class . 'users.php',
    'Services'              => $pre_class . 'services.php',
    'RehabGallery'          => $pre_class . 'rehabgallery.php',
    'RehabCenters'          => $pre_class . 'rehabcenters.php',
    'Admission'             => $pre_class . 'admission.php',
    'AdmissionServices'     => $pre_class . 'admission_services.php',
    'Inputs'                => $pre_class . 'inputs.php',
    'Appointments'          => $pre_class . 'appointments.php',
    'Payments'          => $pre_class . 'payments.php',
);

$classes['Connection'] = "classes/defaults/class.connection.php";
$classes['Routes'] = "classes/defaults/class.routes.php";
