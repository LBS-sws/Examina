<?php

return array(
    'examina'=>array(
        'access'=>'EM',
		'icon'=>'fa-pencil',
        'items'=>array(
            'My test'=>array(
                'access'=>'EM01',
                'url'=>'/myTest/index',
            ),
            'Simulation test'=>array(
                'access'=>'EM02',
                'url'=>'/simTest/index',
            ),
        ),
    ),
    'System Setting'=>array(
        'access'=>'SS',
		'icon'=>'fa-gear',
        'items'=>array(
            'Test list'=>array(
                'access'=>'SS02',
                'url'=>'/testTop/index',
            ),
            'menu setting'=>array(
                'access'=>'SS03',
                'url'=>'/menuSet/index',
            ),
        ),
    ),
    'statistical'=>array(
        'access'=>'SC',
		'icon'=>'fa-bar-chart',
        'items'=>array(
            'Test results statistics'=>array(
                'access'=>'SC01',
                'url'=>'/statisticsTest/index',
            ),
            'Title results statistics'=>array(
                'access'=>'SC02',
                'url'=>'/statisticsTitle/index',
            ),
            'Staff results statistics'=>array(
                'access'=>'SC03',
                'url'=>'/statisticsStaff/index',
            ),
            'Quiz results statistics'=>array(
                'access'=>'SC04',
                'url'=>'/statisticsQuiz/index',
            ),
        ),
    ),
    'training process'=>array(
        'access'=>'TP',
		'icon'=>'fa-superpowers',
        'items'=>array(
            'Enterprise situation'=>array(
                'access'=>'TP01',
                'url'=>'/enterprise/index',
            ),
            'Operation study'=>array(
                'access'=>'TP02',
                'url'=>'/practice/index',
            ),
            'Theoretical knowledge'=>array(
                'access'=>'TP03',
                'url'=>'/Theory/index',
            ),
            'Communication answer'=>array(
                'access'=>'TP04',
                'url'=>'/answer/index',
            ),
            'exam(Theory + practice)'=>array(
                'access'=>'TP05',
                'url'=>'/examTheory/index',
            ),
            'System exam(In previous)'=>array(
                'access'=>'TP06',
                'url'=>'/examPrevious/index',
            ),
        ),
    ),
);
