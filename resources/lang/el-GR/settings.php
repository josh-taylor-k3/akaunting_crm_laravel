<?php

return [

    'company' => [
        'name'              => 'Όνομα',
        'email'             => 'Διεύθυνση ηλ. ταχυδρομείου',
        'phone'             => 'Τηλέφωνο',
        'address'           => 'Διεύθυνση',
        'logo'              => 'Λογότυπο',
    ],
    'localisation' => [
        'tab'               => 'Τοπική προσαρμογή',
        'date' => [
            'format'        => 'Μορφοποίηση Ημερομηνίας',
            'separator'     => 'Διαχωριστικό ημερομηνίας',
            'dash'          => 'Παύλα (-)',
            'dot'           => 'Τελεία (.)',
            'comma'         => 'Κόμμα (,)',
            'slash'         => 'Πλαγιοκάθετος (/)',
            'space'         => 'Κενό ( )',
        ],
        'timezone'          => 'Ζώνη ώρας',
        'percent' => [
            'title'         => 'Θέση συμβόλου ποσοστού (%)',
            'before'        => 'Πριν από τον αριθμό',
            'after'         => 'Μετά από τον αριθμό',
        ],
    ],
    'invoice' => [
        'tab'               => 'Τιμολόγιο',
        'prefix'            => 'Πρόθεμα αριθμού',
        'digit'             => 'Αριθμός ψηφίων',
        'next'              => 'Επόμενος αριθμός',
        'logo'              => 'Λογότυπο',
        'custom'            => 'Προσαρμογή',
        'item_name'         => 'Όνομα αντικειμένου',
        'item'              => 'Αντικείμενα',
        'product'           => 'Προϊόντα',
        'service'           => 'Υπηρεσίες',
        'price_name'        => 'Όνομα τιμής',
        'price'             => 'Τιμή',
        'rate'              => 'Συντελεστής',
        'quantity_name'     => 'Όνομα ποσότητα',
        'quantity'          => 'Ποσότητα',
    ],
    'default' => [
        'tab'               => 'Προεπιλογές',
        'account'           => 'Προεπιλεγμένος λογαριασμός',
        'currency'          => 'Προεπιλεγμένο νόμισμα',
        'tax'               => 'Προεπιλεγμένη φορολογικός συντελεστής',
        'payment'           => 'Προεπιλεγμένη μέθοδος πληρωμής',
        'language'          => 'Προεπιλεγμένη Γλώσσα',
    ],
    'email' => [
        'protocol'          => 'Πρωτόκολλο',
        'php'               => 'PHP Mail',
        'smtp' => [
            'name'          => 'SMTP',
            'host'          => 'Διακομιστής SMTP',
            'port'          => 'Θύρα SMTP',
            'username'      => 'Όνομα Χρήστη SMTP',
            'password'      => 'Συνθηματικό SMTP',
            'encryption'    => 'Ασφάλεια SMTP',
            'none'          => 'Κανένα',
        ],
        'sendmail'          => 'Απεσταλμένα μηνύματα',
        'sendmail_path'     => 'Διαδρομή προς Απεσταλμένα Μηνύματα',
        'log'               => 'Αρχείο καταγραφής ηλεκτρονικών μηνυμάτων',
    ],
    'scheduling' => [
        'tab'               => 'Χρονοδιαγραμα',
        'send_invoice'      => 'Υπενθύμιση αποστολής τιμολογίου',
        'invoice_days'      => 'Αποστολή μετά από πόσες μέρες πρέπει να εξοφληθεί',
        'send_bill'         => 'Αποστολή υπενθύμισης Λογαριασμού',
        'bill_days'         => 'Αποστολή πριν από πόσες μέρες θα έπρεπε να είχε εξοφληθεί',
        'cron_command'      => 'Προγραμματισμένη Εντολή',
        'schedule_time'     => 'Ώρα εκτέλεσης',
        'send_item_reminder'=> 'Αποστολή Υπενθύμισης Αντικειμένου',
        'item_stocks'       => 'Αποστολή σε Απόθεμα Αντικειμένου',
    ],
    'appearance' => [
        'tab'               => 'Εμφάνιση',
        'theme'             => 'Θέμα',
        'light'             => 'Φωτεινό',
        'dark'              => 'Σκοτεινό',
        'list_limit'        => 'Εγγραφές ανά σελίδα',
        'use_gravatar'      => 'Χρήση gravatar',
    ],
    'system' => [
        'tab'               => 'Σύστημα',
        'session' => [
            'lifetime'      => 'Διάρκεια συνεδρίας (λεπτά)',
            'handler'       => 'Διαχειριστής συνεδρίας',
            'file'          => 'Αρχείο',
            'database'      => 'Βάση δεδομένων',
        ],
        'file_size'         => 'Μέγιστο μέγεθος αρχείου (MB)',
        'file_types'        => 'Επιτρεπόμενα αρχεία',
    ],

];
