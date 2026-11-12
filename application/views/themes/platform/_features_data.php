<?php
// The /features page: every sidebar menu of a school, what it is for and which roles have it by default.
// Generated from the menu tables of a new school (default role permissions); edit the texts here.
return array (
  'areas' => 
  array (
    0 => 
    array (
      'key' => 'students',
      'title' => 'Students and admissions',
      'intro' => 'From the first enquiry to the day a student leaves: admissions, records, attendance, conduct and certificates.',
      'icon' => 'users',
      'modules' => 
      array (
        0 => 
        array (
          'name' => 'Front Office',
          'what' => 'The school reception: enquiries from prospective parents, visitors, calls, post and complaints.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Receptionist',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Admission Enquiry',
              'what' => 'Log enquiries from prospective parents and follow them up until they apply or drop off.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Receptionist',
              ),
            ),
            1 => 
            array (
              'name' => 'Visitor Book',
              'what' => 'Sign visitors in and out, with purpose, ID and who they came to see.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Receptionist',
              ),
            ),
            2 => 
            array (
              'name' => 'Phone Call Log',
              'what' => 'Record incoming and outgoing phone calls and follow-up dates.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Receptionist',
              ),
            ),
            3 => 
            array (
              'name' => 'Postal Dispatch',
              'what' => 'Record post and parcels sent out by the school.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Receptionist',
              ),
            ),
            4 => 
            array (
              'name' => 'Postal Receive',
              'what' => 'Record post and parcels the school receives.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Receptionist',
              ),
            ),
            5 => 
            array (
              'name' => 'Complain',
              'what' => 'Log complaints, assign them and record the action taken.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Receptionist',
              ),
            ),
            6 => 
            array (
              'name' => 'Setup Front Office',
              'what' => 'Set the lists used by the forms above: visit purposes, complaint types, sources, references.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Receptionist',
              ),
            ),
          ),
        ),
        1 => 
        array (
          'name' => 'Student Information',
          'what' => 'Every student record: admitting, finding, editing and organising students.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
            2 => 'Receptionist',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Student Details',
              'what' => 'Find students by class or keyword; open a profile to edit details, guardians, documents, fees and timeline.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Receptionist',
              ),
            ),
            1 => 
            array (
              'name' => 'Student Admission',
              'what' => 'Admit a new student: class, section, personal and guardian details. The admission number is generated if numbering is automatic.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            2 => 
            array (
              'name' => 'Online Admission',
              'what' => 'Applications made on the website: review, check the form fee (online or bank transfer receipt), then admit the applicant as a student.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            3 => 
            array (
              'name' => 'Disabled Students',
              'what' => 'Students who left or were disabled, with the reason; enable them again from here.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            4 => 
            array (
              'name' => 'Multi Class Student',
              'what' => 'Put one student in more than one class (e.g. for electives).',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            5 => 
            array (
              'name' => 'Bulk Delete',
              'what' => 'Delete several students of a class at once.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            6 => 
            array (
              'name' => 'Student Categories',
              'what' => 'Student categories (e.g. day, boarding, scholarship) used for filtering and fees.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            7 => 
            array (
              'name' => 'Student House',
              'what' => 'School houses students belong to.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            8 => 
            array (
              'name' => 'Disable Reason',
              'what' => 'Reasons offered when a student is disabled (graduated, transferred...).',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
          ),
        ),
        2 => 
        array (
          'name' => 'Attendance',
          'what' => 'Daily student attendance and student leave.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Student Attendance',
              'what' => 'Mark today\'s attendance for a class (present, late, absent, half day, holiday).',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            1 => 
            array (
              'name' => 'Approve Leave',
              'what' => 'Approve or reject leave requested by students and parents.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            2 => 
            array (
              'name' => 'Attendance By Date',
              'what' => 'Attendance of a class on a given date.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
          ),
        ),
        3 => 
        array (
          'name' => 'QR Code Attendance',
          'what' => 'Attendance by scanning student or staff ID-card QR codes with a phone or webcam.',
          'who' => 
          array (
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Attendance',
              'what' => 'Open the scanner and scan ID cards to mark attendance.',
              'who' => 
              array (
              ),
            ),
            1 => 
            array (
              'name' => 'Setting',
              'what' => 'Which attendance a scan records and camera options.',
              'who' => 
              array (
              ),
            ),
          ),
        ),
        4 => 
        array (
          'name' => 'Behaviour Records',
          'what' => 'Recording incidents and good conduct, with points that add up per student.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
            2 => 'Accountant',
            3 => 'Librarian',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Assign Incident',
              'what' => 'Record an incident against a student; its points are added to the student\'s total.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Librarian',
              ),
            ),
            1 => 
            array (
              'name' => 'Incidents',
              'what' => 'The incidents the school uses, each with positive or negative points.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Librarian',
              ),
            ),
            2 => 
            array (
              'name' => 'Reports',
              'what' => 'Points and incidents by student, class or house.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Librarian',
              ),
            ),
            3 => 
            array (
              'name' => 'Setting',
              'what' => 'Whether parents and students can see incidents and comment.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
          ),
        ),
        5 => 
        array (
          'name' => 'Certificate',
          'what' => 'Designing and printing certificates and ID cards.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
            2 => 'Accountant',
            3 => 'Receptionist',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Student Certificate',
              'what' => 'Design student certificates (leaving, conduct...).',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            1 => 
            array (
              'name' => 'Generate Certificate',
              'what' => 'Print a certificate for selected students.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            2 => 
            array (
              'name' => 'Student ID Card',
              'what' => 'Design student ID cards.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            3 => 
            array (
              'name' => 'Generate ID Card',
              'what' => 'Print ID cards for a class.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            4 => 
            array (
              'name' => 'Staff ID Card',
              'what' => 'Design staff ID cards.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Receptionist',
              ),
            ),
            5 => 
            array (
              'name' => 'Generate Staff ID Card',
              'what' => 'Print staff ID cards.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Receptionist',
              ),
            ),
          ),
        ),
        6 => 
        array (
          'name' => 'Alumni',
          'what' => 'Former students and alumni events.',
          'who' => 
          array (
            0 => 'Admin',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Manage Alumni',
              'what' => 'Former students and their current details.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            1 => 
            array (
              'name' => 'Events',
              'what' => 'Alumni events and invitations.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
          ),
        ),
      ),
    ),
    1 => 
    array (
      'key' => 'money',
      'title' => 'Fees and finance',
      'intro' => 'Fees by class and term, online and bank-transfer payments, and the school\'s income and expenses.',
      'icon' => 'wallet',
      'modules' => 
      array (
        0 => 
        array (
          'name' => 'Fees Collection',
          'what' => 'Setting up fees and taking payments. Fees are built as Type, then Group, then Master (amounts per class), then assigned to students.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Accountant',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Collect Fees',
              'what' => 'Find a student, take a full or part payment, print the receipt, or print a fee bill showing what is still owed and how to pay.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            1 => 
            array (
              'name' => 'Offline Bank Payments',
              'what' => 'Bank transfers parents uploaded from their panel: approve to record the payment, or reject.',
              'who' => 
              array (
              ),
            ),
            2 => 
            array (
              'name' => 'Search Fees Payment',
              'what' => 'Find a payment by receipt/payment ID to view, print or revert it.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            3 => 
            array (
              'name' => 'Search Due Fees',
              'what' => 'Every student with unpaid fees, by class; remind a guardian on WhatsApp in one tap.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            4 => 
            array (
              'name' => 'Fees Master',
              'what' => 'Set the amount and due date of each fee type per fee group, then assign the group to classes or students.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            5 => 
            array (
              'name' => 'Fees Group',
              'what' => 'Groups of fees charged together (e.g. "JSS1 First Term").',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            6 => 
            array (
              'name' => 'Fees Type',
              'what' => 'Kinds of fee (tuition, uniform, PTA levy...).',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            7 => 
            array (
              'name' => 'Fees Discount',
              'what' => 'Discounts (sibling, scholarship) and the students they apply to.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            8 => 
            array (
              'name' => 'Fees Carry Forward',
              'what' => 'Carry unpaid balances from the last session into the new one.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            9 => 
            array (
              'name' => 'Fees Reminder',
              'what' => 'Automatic email/SMS reminders a set number of days before and after fees are due.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
          ),
        ),
        1 => 
        array (
          'name' => 'Income',
          'what' => 'Money the school receives outside fees (donations, rent, sales).',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Accountant',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Add Income',
              'what' => 'Record money received, with head, date, amount and an optional document.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            1 => 
            array (
              'name' => 'Search Income',
              'what' => 'Find income entries by date range or keyword.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            2 => 
            array (
              'name' => 'Income Head',
              'what' => 'Income categories.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
          ),
        ),
        2 => 
        array (
          'name' => 'Expenses',
          'what' => 'Money the school spends, by category.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Accountant',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Add Expense',
              'what' => 'Record money spent, with head, date, amount and receipt.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            1 => 
            array (
              'name' => 'Search Expense',
              'what' => 'Find expense entries by date range or keyword.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            2 => 
            array (
              'name' => 'Expense Head',
              'what' => 'Expense categories.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
          ),
        ),
      ),
    ),
    2 => 
    array (
      'key' => 'teaching',
      'title' => 'Teaching and results',
      'intro' => 'Classes and timetables, lessons and homework, computer-based tests and Nigerian term report cards.',
      'icon' => 'book',
      'modules' => 
      array (
        0 => 
        array (
          'name' => 'Academics',
          'what' => 'The school structure: classes, sections, subjects, timetables and promotion.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
            2 => 'Receptionist',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Class Timetable',
              'what' => 'Build and view the weekly timetable of a class.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Receptionist',
              ),
            ),
            1 => 
            array (
              'name' => 'Teachers Timetable',
              'what' => 'Each teacher\'s weekly timetable.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            2 => 
            array (
              'name' => 'Assign Class Teacher',
              'what' => 'Make a teacher the class teacher of a class/section.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Receptionist',
              ),
            ),
            3 => 
            array (
              'name' => 'Promote Students',
              'what' => 'Move students to the next class for a new session, with pass/fail.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            4 => 
            array (
              'name' => 'Subject Group',
              'what' => 'The subjects each class takes.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Receptionist',
              ),
            ),
            5 => 
            array (
              'name' => 'Subjects',
              'what' => 'All subjects (theory or practical).',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Receptionist',
              ),
            ),
            6 => 
            array (
              'name' => 'Class',
              'what' => 'Classes and the sections in each.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Receptionist',
              ),
            ),
            7 => 
            array (
              'name' => 'Sections',
              'what' => 'Section names (A, B, Gold...).',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Receptionist',
              ),
            ),
          ),
        ),
        1 => 
        array (
          'name' => 'Lesson Plan',
          'what' => 'Lessons, topics and how far each class has got through its syllabus.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Copy Old Lessons',
              'what' => 'Copy lessons and topics from a previous session.',
              'who' => 
              array (
              ),
            ),
            1 => 
            array (
              'name' => 'Manage Lesson Plan',
              'what' => 'Plan each period: lesson, topic, teaching method and materials.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            2 => 
            array (
              'name' => 'Manage Syllabus Status',
              'what' => 'Mark topics as completed to track syllabus progress.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            3 => 
            array (
              'name' => 'Lesson',
              'what' => 'Lessons per class and subject.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            4 => 
            array (
              'name' => 'Topic',
              'what' => 'Topics within each lesson.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
          ),
        ),
        2 => 
        array (
          'name' => 'Homework',
          'what' => 'Homework set by teachers, submitted and marked online.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Add Homework',
              'what' => 'Set homework for a class and subject, see submissions and mark them.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            1 => 
            array (
              'name' => 'Daily Assignment',
              'what' => 'Daily assignments students submit and teachers evaluate.',
              'who' => 
              array (
              ),
            ),
          ),
        ),
        3 => 
        array (
          'name' => 'Report Cards',
          'what' => 'Nigerian term report cards: First, Second and Third Term, CA and exam scores, WAEC grades (A1-F9), position in class, affective and psychomotor ratings, and teacher and principal remarks.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Exam',
              'what' => 'Create a term exam, add its subjects and enter marks per assessment.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            1 => 
            array (
              'name' => 'Exam Schedule',
              'what' => 'The exam timetable students and parents see.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            2 => 
            array (
              'name' => 'Print Marksheet',
              'what' => 'Print term report cards for a class with the chosen template.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            3 => 
            array (
              'name' => 'Exam Grade',
              'what' => 'Grade bands (A1, B2...) used on report cards.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            4 => 
            array (
              'name' => 'Assign Observation',
              'what' => 'Rate students on observations such as conduct or punctuality.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            5 => 
            array (
              'name' => 'Observation',
              'what' => 'The observations rated on report cards.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            6 => 
            array (
              'name' => 'Observation Parameter',
              'what' => 'The rating scale used for observations.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            7 => 
            array (
              'name' => 'Assessment',
              'what' => 'Assessment types and their maximum marks (CA 1, CA 2, Exam...).',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            8 => 
            array (
              'name' => 'Term',
              'what' => 'The terms of the session.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            9 => 
            array (
              'name' => 'Template',
              'what' => 'Report card layouts: header, which fields and signatures appear.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            10 => 
            array (
              'name' => 'Reports',
              'what' => 'Subject, class and term result analysis.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            11 => 
            array (
              'name' => 'Setting',
              'what' => 'Report card options.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
          ),
        ),
        4 => 
        array (
          'name' => 'Online Examinations',
          'what' => 'Computer-based tests (CBT) students take online, built from a question bank.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Online Exam',
              'what' => 'Create a CBT: dates, duration, pass mark, questions from the bank, and the classes that take it. Results can feed report-card marks.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            1 => 
            array (
              'name' => 'Question Bank',
              'what' => 'Add questions (single/multiple choice, true/false, descriptive, passages) by subject and class, or import them.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
          ),
        ),
        5 => 
        array (
          'name' => 'Download Center',
          'what' => 'Files and videos shared with classes (notes, assignments, syllabus).',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
            2 => 'Accountant',
            3 => 'Librarian',
            4 => 'Receptionist',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Content Type',
              'what' => 'Kinds of content (assignment, study material, syllabus).',
              'who' => 
              array (
              ),
            ),
            1 => 
            array (
              'name' => 'Content Share List',
              'what' => 'Content already shared, and with whom.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Librarian',
                4 => 'Receptionist',
              ),
            ),
            2 => 
            array (
              'name' => 'Upload /Share Content',
              'what' => 'Upload files and share them with classes, students or staff.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            3 => 
            array (
              'name' => 'Video Tutorial',
              'what' => 'Share video links with classes.',
              'who' => 
              array (
              ),
            ),
          ),
        ),
        6 => 
        array (
          'name' => 'Online Course',
          'what' => 'Paid or free courses students follow online at their own pace (add-on).',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
            2 => 'Accountant',
            3 => 'Librarian',
            4 => 'Receptionist',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Online Course',
              'what' => 'Create courses with sections, lessons, videos and quizzes; set price and publish.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            1 => 
            array (
              'name' => 'Offline Payment',
              'what' => 'Approve course payments made by bank transfer.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            2 => 
            array (
              'name' => 'Course Category',
              'what' => 'Categories courses are listed under.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Librarian',
                4 => 'Receptionist',
              ),
            ),
            3 => 
            array (
              'name' => 'Online Course Report',
              'what' => 'Course sales, students enrolled and their progress.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Librarian',
                4 => 'Receptionist',
              ),
            ),
            4 => 
            array (
              'name' => 'Setting',
              'what' => 'Who can buy courses (students, guests) and payment options.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
          ),
        ),
        7 => 
        array (
          'name' => 'Zoom Live Classes',
          'what' => 'Live classes and staff meetings through Zoom (add-on).',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
            2 => 'Accountant',
            3 => 'Librarian',
            4 => 'Receptionist',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Live Meeting',
              'what' => 'Schedule Zoom meetings with staff.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Librarian',
                4 => 'Receptionist',
              ),
            ),
            1 => 
            array (
              'name' => 'Live Classes',
              'what' => 'Schedule a Zoom class for a class/section; students join from their panel.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Librarian',
                3 => 'Receptionist',
              ),
            ),
            2 => 
            array (
              'name' => 'Live Class Report',
              'what' => 'Who joined each live class.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Librarian',
                4 => 'Receptionist',
              ),
            ),
            3 => 
            array (
              'name' => 'Live Meeting Report',
              'what' => 'Who joined each staff meeting.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Librarian',
                4 => 'Receptionist',
              ),
            ),
            4 => 
            array (
              'name' => 'Setting',
              'what' => 'The school\'s Zoom API keys and options.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
          ),
        ),
        8 => 
        array (
          'name' => 'Gmeet Live Classes',
          'what' => 'Live classes and staff meetings through Google Meet (add-on).',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
            2 => 'Accountant',
            3 => 'Librarian',
            4 => 'Receptionist',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Live Classes',
              'what' => 'Schedule a Google Meet class for a class/section; students join from their panel.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Librarian',
                3 => 'Receptionist',
              ),
            ),
            1 => 
            array (
              'name' => 'Live Meeting',
              'what' => 'Schedule Google Meet meetings with staff.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Librarian',
                4 => 'Receptionist',
              ),
            ),
            2 => 
            array (
              'name' => 'Live Class Report',
              'what' => 'Who joined each live class.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            3 => 
            array (
              'name' => 'Live Meeting Report',
              'what' => 'Who joined each staff meeting.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
              ),
            ),
            4 => 
            array (
              'name' => 'Setting',
              'what' => 'Google Meet settings for the school.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
          ),
        ),
      ),
    ),
    3 => 
    array (
      'key' => 'people',
      'title' => 'Staff and communication',
      'intro' => 'Staff records, leave and payroll, and messages to parents, students and staff.',
      'icon' => 'message',
      'modules' => 
      array (
        0 => 
        array (
          'name' => 'Human Resource',
          'what' => 'Staff records, staff attendance, leave and payroll.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
            2 => 'Accountant',
            3 => 'Librarian',
            4 => 'Receptionist',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Staff Directory',
              'what' => 'Staff records: add staff, their role, login, documents and salary.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Librarian',
                4 => 'Receptionist',
              ),
            ),
            1 => 
            array (
              'name' => 'Staff Attendance',
              'what' => 'Mark daily staff attendance.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            2 => 
            array (
              'name' => 'Payroll',
              'what' => 'Monthly pay with allowances and deductions, including pension, NHF and PAYE worked out for you, and payslips.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            3 => 
            array (
              'name' => 'Approve Leave Request',
              'what' => 'Approve or reject staff leave.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            4 => 
            array (
              'name' => 'Apply Leave',
              'what' => 'Apply for your own leave.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
              ),
            ),
            5 => 
            array (
              'name' => 'Leave Type',
              'what' => 'Leave types (sick, casual, maternity).',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            6 => 
            array (
              'name' => 'Teachers Rating',
              'what' => 'Ratings students gave teachers; approve them to show.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            7 => 
            array (
              'name' => 'Department',
              'what' => 'Staff departments.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            8 => 
            array (
              'name' => 'Designation',
              'what' => 'Staff job titles.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            9 => 
            array (
              'name' => 'Disabled Staff',
              'what' => 'Staff who left; enable them again from here.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
          ),
        ),
        1 => 
        array (
          'name' => 'Communicate',
          'what' => 'Notices, email and SMS to students, parents and staff.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
            2 => 'Accountant',
            3 => 'Librarian',
            4 => 'Receptionist',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Notice Board',
              'what' => 'Post notices to students, parents and staff, and share them to WhatsApp groups.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Librarian',
                4 => 'Receptionist',
              ),
            ),
            1 => 
            array (
              'name' => 'Send Email',
              'what' => 'Email a group, individuals, a class or birthdays, now or scheduled, with templates.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Librarian',
                4 => 'Receptionist',
              ),
            ),
            2 => 
            array (
              'name' => 'Send SMS',
              'what' => 'Text a group, individuals, a class or birthdays through the school\'s SMS gateway (charged by the provider).',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Librarian',
                4 => 'Receptionist',
              ),
            ),
            3 => 
            array (
              'name' => 'Email / SMS Log',
              'what' => 'Every email and SMS sent, with recipients.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Librarian',
                4 => 'Receptionist',
              ),
            ),
            4 => 
            array (
              'name' => 'Schedule Email SMS Log',
              'what' => 'Scheduled messages: edit or delete before they go.',
              'who' => 
              array (
              ),
            ),
            5 => 
            array (
              'name' => 'Login Credentials Send',
              'what' => 'Email or text students and parents their login details.',
              'who' => 
              array (
              ),
            ),
            6 => 
            array (
              'name' => 'Email Template',
              'what' => 'Reusable email texts with attachments.',
              'who' => 
              array (
              ),
            ),
            7 => 
            array (
              'name' => 'SMS Template',
              'what' => 'Reusable SMS texts.',
              'who' => 
              array (
              ),
            ),
          ),
        ),
        2 => 
        array (
          'name' => 'Two Factor Authentication',
          'what' => 'Sign-in with a 6-digit code from Google Authenticator for extra security.',
          'who' => 
          array (
            0 => 'Admin',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Setup 2FA',
              'what' => 'Scan the QR code with Google Authenticator to turn on your own two-factor sign-in.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            1 => 
            array (
              'name' => 'Setting',
              'what' => 'Decide which user types must use two-factor sign-in.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
          ),
        ),
      ),
    ),
    4 => 
    array (
      'key' => 'facilities',
      'title' => 'Facilities',
      'intro' => 'The rest of the school\'s operations: library, store, buses and boarding.',
      'icon' => 'building',
      'modules' => 
      array (
        0 => 
        array (
          'name' => 'Library',
          'what' => 'Books, library members, issuing and returning.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Librarian',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Book List',
              'what' => 'The book catalogue: add, edit and import books.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Librarian',
              ),
            ),
            1 => 
            array (
              'name' => 'Issue - Return',
              'what' => 'Issue a book to a member and record its return.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Librarian',
              ),
            ),
            2 => 
            array (
              'name' => 'Add Student',
              'what' => 'Give students a library card.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Librarian',
              ),
            ),
            3 => 
            array (
              'name' => 'Add Staff Member',
              'what' => 'Give staff a library card.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Librarian',
              ),
            ),
          ),
        ),
        1 => 
        array (
          'name' => 'Inventory',
          'what' => 'School stock: items, stores, suppliers and items issued to staff.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Accountant',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Issue Item',
              'what' => 'Issue items to staff and mark them returned.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            1 => 
            array (
              'name' => 'Add Item Stock',
              'what' => 'Record stock received.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            2 => 
            array (
              'name' => 'Add Item',
              'what' => 'Items kept in stock.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            3 => 
            array (
              'name' => 'Item Category',
              'what' => 'Item categories.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            4 => 
            array (
              'name' => 'Item Store',
              'what' => 'Store rooms.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            5 => 
            array (
              'name' => 'Item Supplier',
              'what' => 'Suppliers and their contacts.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
          ),
        ),
        2 => 
        array (
          'name' => 'Transport',
          'what' => 'School buses: routes, pickup points, vehicles and transport fees.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Accountant',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Fees Master',
              'what' => 'Transport fee due dates per month.',
              'who' => 
              array (
              ),
            ),
            1 => 
            array (
              'name' => 'Pickup Point',
              'what' => 'Pickup points.',
              'who' => 
              array (
              ),
            ),
            2 => 
            array (
              'name' => 'Routes',
              'what' => 'Bus routes.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            3 => 
            array (
              'name' => 'Vehicles',
              'what' => 'Vehicles and drivers.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            4 => 
            array (
              'name' => 'Assign Vehicle',
              'what' => 'Which vehicles run each route.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            5 => 
            array (
              'name' => 'Route Pickup Point',
              'what' => 'Pickup points on each route, with fees and times.',
              'who' => 
              array (
              ),
            ),
            6 => 
            array (
              'name' => 'Student Transport Fees',
              'what' => 'Which students use transport and for which months.',
              'who' => 
              array (
              ),
            ),
          ),
        ),
        3 => 
        array (
          'name' => 'Hostel',
          'what' => 'Boarding houses and rooms, and which students are in them.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Accountant',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Hostel Rooms',
              'what' => 'Rooms, beds and cost; students are assigned from their profile.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            1 => 
            array (
              'name' => 'Room Type',
              'what' => 'Room types.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            2 => 
            array (
              'name' => 'Hostel',
              'what' => 'Hostels (boys, girls...).',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
          ),
        ),
      ),
    ),
    5 => 
    array (
      'key' => 'website',
      'title' => 'Website, reports and settings',
      'intro' => 'The school\'s own website, reports across every module, and how the school is set up.',
      'icon' => 'globe',
      'modules' => 
      array (
        0 => 
        array (
          'name' => 'Front CMS',
          'what' => 'The school\'s public website at its own address, edited with a block editor; news, events and gallery stay up to date.',
          'who' => 
          array (
            0 => 'Admin',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Website Design',
              'what' => 'Theme, colours and fonts of the website, with preview.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            1 => 
            array (
              'name' => 'Event',
              'what' => 'Events shown on the website.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            2 => 
            array (
              'name' => 'Gallery',
              'what' => 'Photo galleries on the website.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            3 => 
            array (
              'name' => 'News',
              'what' => 'News posts on the website.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            4 => 
            array (
              'name' => 'Media Manager',
              'what' => 'Upload and manage images and files used on the website.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            5 => 
            array (
              'name' => 'Pages',
              'what' => 'Website pages, edited with the block editor; publish, preview and restore earlier versions.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            6 => 
            array (
              'name' => 'Menus',
              'what' => 'The website\'s top and footer menus.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            7 => 
            array (
              'name' => 'Banner Images',
              'what' => 'Banner images at the top of the website.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
          ),
        ),
        1 => 
        array (
          'name' => 'Reports',
          'what' => 'Read-only reports across every module, with export to Excel, CSV and PDF.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
            2 => 'Accountant',
            3 => 'Librarian',
            4 => 'Receptionist',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Student Information',
              'what' => 'Student lists, guardians, admissions, sibling, history and login details reports.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            1 => 
            array (
              'name' => 'Finance',
              'what' => 'Fee collection, balances, income, expenses, payroll and online payment reports.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
              ),
            ),
            2 => 
            array (
              'name' => 'Attendance',
              'what' => 'Student and staff attendance reports, including low attendance.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
              ),
            ),
            3 => 
            array (
              'name' => 'Examinations',
              'what' => 'Exam rank and result reports.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            4 => 
            array (
              'name' => 'Online Examinations',
              'what' => 'CBT results and attempts.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            5 => 
            array (
              'name' => 'Lesson Plan',
              'what' => 'Syllabus progress reports.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            6 => 
            array (
              'name' => 'Human Resource',
              'what' => 'Staff and payroll reports.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            7 => 
            array (
              'name' => 'Homework',
              'what' => 'Homework and assignment evaluation reports.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            8 => 
            array (
              'name' => 'Library',
              'what' => 'Books issued, due and inventory.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Librarian',
              ),
            ),
            9 => 
            array (
              'name' => 'Inventory',
              'what' => 'Stock, issued and added items.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            10 => 
            array (
              'name' => 'Transport',
              'what' => 'Students per route and pickup point.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
              ),
            ),
            11 => 
            array (
              'name' => 'Hostel',
              'what' => 'Students per hostel and room.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
              ),
            ),
            12 => 
            array (
              'name' => 'Alumni',
              'what' => 'Alumni by session and class.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
                2 => 'Accountant',
                3 => 'Receptionist',
              ),
            ),
            13 => 
            array (
              'name' => 'User Log',
              'what' => 'Who signed in, when and from where.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            14 => 
            array (
              'name' => 'Audit Trail Report',
              'what' => 'Who added, changed or deleted what.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
          ),
        ),
        2 => 
        array (
          'name' => 'System Setting',
          'what' => 'How the school is configured: details, sessions, messaging, payments, roles and modules.',
          'who' => 
          array (
            0 => 'Admin',
            1 => 'Teacher',
            2 => 'Accountant',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'General Setting',
              'what' => 'School name, logos, address, date format, session, admission/staff numbering and other basics.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            1 => 
            array (
              'name' => 'Fees Setting',
              'what' => 'How fees work: carry forward, fines, offline bank payments, receipt options.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            2 => 
            array (
              'name' => 'Session Setting',
              'what' => 'Academic sessions (e.g. 2026-27); choose the current one.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            3 => 
            array (
              'name' => 'Notification Setting',
              'what' => 'Which events send email/SMS/app notifications, and the text of each.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            4 => 
            array (
              'name' => 'SMS Setting',
              'what' => 'Connect the school\'s SMS account (Termii and others); messages go out on the DND route so every parent receives them.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            5 => 
            array (
              'name' => 'Email Setting',
              'what' => 'How the school\'s emails are sent: mail server and sender details.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            6 => 
            array (
              'name' => 'Payment Methods',
              'what' => 'Choose the online payment gateway (Paystack, Flutterwave, Monnify, Interswitch or Remita) and the school\'s bank details for transfers.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            7 => 
            array (
              'name' => 'Print Header Footer',
              'what' => 'Header image and footer text on printed receipts and payslips.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Accountant',
              ),
            ),
            8 => 
            array (
              'name' => 'Front CMS Setting',
              'what' => 'Turn the website on or off, logo, favicon, footer text and sidebar options.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            9 => 
            array (
              'name' => 'Roles Permissions',
              'what' => 'Roles and exactly what each can view, add, edit and delete. This decides which sidebar entries each role sees.',
              'who' => 
              array (
              ),
            ),
            10 => 
            array (
              'name' => 'Languages',
              'what' => 'Which languages the school uses and the default one.',
              'who' => 
              array (
              ),
            ),
            11 => 
            array (
              'name' => 'Currency',
              'what' => 'Currencies, their rates and symbols.',
              'who' => 
              array (
              ),
            ),
            12 => 
            array (
              'name' => 'Users',
              'what' => 'Turn student and parent logins on or off, per person.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            13 => 
            array (
              'name' => 'Modules',
              'what' => 'Turn modules on or off for staff (System), students and parents. Hidden modules disappear from every sidebar.',
              'who' => 
              array (
              ),
            ),
            14 => 
            array (
              'name' => 'Custom Fields',
              'what' => 'Extra fields on the student and staff forms.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            15 => 
            array (
              'name' => 'Captcha Setting',
              'what' => 'Where a captcha is shown (logins, admission form, contact form).',
              'who' => 
              array (
              ),
            ),
            16 => 
            array (
              'name' => 'System Fields',
              'what' => 'Which standard fields appear on the student and staff forms.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            17 => 
            array (
              'name' => 'Student Profile Update',
              'what' => 'Which profile fields students and parents may change themselves.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
            18 => 
            array (
              'name' => 'Online Admission',
              'what' => 'Open or close online admission: dates, classes and places, form fee, the form\'s fields and the school\'s own questions.',
              'who' => 
              array (
                0 => 'Admin',
                1 => 'Teacher',
              ),
            ),
            19 => 
            array (
              'name' => 'File Types',
              'what' => 'Allowed upload file types and sizes.',
              'who' => 
              array (
              ),
            ),
            20 => 
            array (
              'name' => 'Sidebar Menu',
              'what' => 'Reorder, rename or hide sidebar menus and entries for the school.',
              'who' => 
              array (
              ),
            ),
          ),
        ),
        3 => 
        array (
          'name' => 'Help & Support',
          'what' => 'Asking the EduSYS team for help, and following the replies.',
          'who' => 
          array (
            0 => 'Admin',
          ),
          'entries' => 
          array (
            0 => 
            array (
              'name' => 'Support Requests',
              'what' => 'Open a support request to the EduSYS team and follow the replies.',
              'who' => 
              array (
                0 => 'Admin',
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  'roles' => 
  array (
    0 => 'Admin',
    1 => 'Teacher',
    2 => 'Accountant',
    3 => 'Librarian',
    4 => 'Receptionist',
  ),
);
