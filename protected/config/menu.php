<?php

return array(
    'examina'=>array(
        'access'=>'EM',
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
        'items'=>array(
            'Exam question bank'=>array(
                'access'=>'SS01',
                'url'=>'/question/index',
            ),
            'Test list'=>array(
                'access'=>'SS02',
                'url'=>'/testTop/index',
            ),
        ),
    ),
    'statistical'=>array(
        'access'=>'SC',
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
        ),
    ),
);
