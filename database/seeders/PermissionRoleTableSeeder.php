<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permission_role')->delete();
        
        \DB::table('permission_role')->insert(array (
            0 => 
            array (
                'permission_id' => 1,
                'role_id' => 1,
            ),
            1 => 
            array (
                'permission_id' => 1,
                'role_id' => 3,
            ),
            2 => 
            array (
                'permission_id' => 1,
                'role_id' => 7,
            ),
            3 => 
            array (
                'permission_id' => 2,
                'role_id' => 1,
            ),
            4 => 
            array (
                'permission_id' => 2,
                'role_id' => 7,
            ),
            5 => 
            array (
                'permission_id' => 3,
                'role_id' => 1,
            ),
            6 => 
            array (
                'permission_id' => 3,
                'role_id' => 7,
            ),
            7 => 
            array (
                'permission_id' => 4,
                'role_id' => 1,
            ),
            8 => 
            array (
                'permission_id' => 4,
                'role_id' => 7,
            ),
            9 => 
            array (
                'permission_id' => 5,
                'role_id' => 1,
            ),
            10 => 
            array (
                'permission_id' => 5,
                'role_id' => 7,
            ),
            11 => 
            array (
                'permission_id' => 6,
                'role_id' => 1,
            ),
            12 => 
            array (
                'permission_id' => 6,
                'role_id' => 7,
            ),
            13 => 
            array (
                'permission_id' => 7,
                'role_id' => 1,
            ),
            14 => 
            array (
                'permission_id' => 7,
                'role_id' => 7,
            ),
            15 => 
            array (
                'permission_id' => 8,
                'role_id' => 1,
            ),
            16 => 
            array (
                'permission_id' => 8,
                'role_id' => 7,
            ),
            17 => 
            array (
                'permission_id' => 9,
                'role_id' => 1,
            ),
            18 => 
            array (
                'permission_id' => 9,
                'role_id' => 7,
            ),
            19 => 
            array (
                'permission_id' => 10,
                'role_id' => 1,
            ),
            20 => 
            array (
                'permission_id' => 10,
                'role_id' => 7,
            ),
            21 => 
            array (
                'permission_id' => 11,
                'role_id' => 1,
            ),
            22 => 
            array (
                'permission_id' => 11,
                'role_id' => 7,
            ),
            23 => 
            array (
                'permission_id' => 12,
                'role_id' => 1,
            ),
            24 => 
            array (
                'permission_id' => 12,
                'role_id' => 7,
            ),
            25 => 
            array (
                'permission_id' => 13,
                'role_id' => 1,
            ),
            26 => 
            array (
                'permission_id' => 13,
                'role_id' => 7,
            ),
            27 => 
            array (
                'permission_id' => 14,
                'role_id' => 1,
            ),
            28 => 
            array (
                'permission_id' => 14,
                'role_id' => 7,
            ),
            29 => 
            array (
                'permission_id' => 15,
                'role_id' => 1,
            ),
            30 => 
            array (
                'permission_id' => 15,
                'role_id' => 7,
            ),
            31 => 
            array (
                'permission_id' => 16,
                'role_id' => 1,
            ),
            32 => 
            array (
                'permission_id' => 16,
                'role_id' => 3,
            ),
            33 => 
            array (
                'permission_id' => 16,
                'role_id' => 7,
            ),
            34 => 
            array (
                'permission_id' => 17,
                'role_id' => 1,
            ),
            35 => 
            array (
                'permission_id' => 17,
                'role_id' => 3,
            ),
            36 => 
            array (
                'permission_id' => 17,
                'role_id' => 7,
            ),
            37 => 
            array (
                'permission_id' => 18,
                'role_id' => 1,
            ),
            38 => 
            array (
                'permission_id' => 18,
                'role_id' => 7,
            ),
            39 => 
            array (
                'permission_id' => 19,
                'role_id' => 1,
            ),
            40 => 
            array (
                'permission_id' => 19,
                'role_id' => 7,
            ),
            41 => 
            array (
                'permission_id' => 20,
                'role_id' => 1,
            ),
            42 => 
            array (
                'permission_id' => 20,
                'role_id' => 7,
            ),
            43 => 
            array (
                'permission_id' => 21,
                'role_id' => 1,
            ),
            44 => 
            array (
                'permission_id' => 21,
                'role_id' => 3,
            ),
            45 => 
            array (
                'permission_id' => 21,
                'role_id' => 7,
            ),
            46 => 
            array (
                'permission_id' => 22,
                'role_id' => 1,
            ),
            47 => 
            array (
                'permission_id' => 22,
                'role_id' => 3,
            ),
            48 => 
            array (
                'permission_id' => 22,
                'role_id' => 7,
            ),
            49 => 
            array (
                'permission_id' => 23,
                'role_id' => 1,
            ),
            50 => 
            array (
                'permission_id' => 23,
                'role_id' => 7,
            ),
            51 => 
            array (
                'permission_id' => 24,
                'role_id' => 1,
            ),
            52 => 
            array (
                'permission_id' => 24,
                'role_id' => 7,
            ),
            53 => 
            array (
                'permission_id' => 25,
                'role_id' => 1,
            ),
            54 => 
            array (
                'permission_id' => 25,
                'role_id' => 7,
            ),
            55 => 
            array (
                'permission_id' => 26,
                'role_id' => 1,
            ),
            56 => 
            array (
                'permission_id' => 26,
                'role_id' => 7,
            ),
            57 => 
            array (
                'permission_id' => 27,
                'role_id' => 1,
            ),
            58 => 
            array (
                'permission_id' => 27,
                'role_id' => 3,
            ),
            59 => 
            array (
                'permission_id' => 27,
                'role_id' => 7,
            ),
            60 => 
            array (
                'permission_id' => 28,
                'role_id' => 1,
            ),
            61 => 
            array (
                'permission_id' => 28,
                'role_id' => 3,
            ),
            62 => 
            array (
                'permission_id' => 28,
                'role_id' => 7,
            ),
            63 => 
            array (
                'permission_id' => 29,
                'role_id' => 1,
            ),
            64 => 
            array (
                'permission_id' => 29,
                'role_id' => 7,
            ),
            65 => 
            array (
                'permission_id' => 30,
                'role_id' => 1,
            ),
            66 => 
            array (
                'permission_id' => 30,
                'role_id' => 7,
            ),
            67 => 
            array (
                'permission_id' => 31,
                'role_id' => 1,
            ),
            68 => 
            array (
                'permission_id' => 31,
                'role_id' => 7,
            ),
            69 => 
            array (
                'permission_id' => 32,
                'role_id' => 1,
            ),
            70 => 
            array (
                'permission_id' => 32,
                'role_id' => 7,
            ),
            71 => 
            array (
                'permission_id' => 33,
                'role_id' => 1,
            ),
            72 => 
            array (
                'permission_id' => 33,
                'role_id' => 3,
            ),
            73 => 
            array (
                'permission_id' => 33,
                'role_id' => 7,
            ),
            74 => 
            array (
                'permission_id' => 34,
                'role_id' => 1,
            ),
            75 => 
            array (
                'permission_id' => 34,
                'role_id' => 7,
            ),
            76 => 
            array (
                'permission_id' => 35,
                'role_id' => 1,
            ),
            77 => 
            array (
                'permission_id' => 35,
                'role_id' => 7,
            ),
            78 => 
            array (
                'permission_id' => 36,
                'role_id' => 1,
            ),
            79 => 
            array (
                'permission_id' => 36,
                'role_id' => 7,
            ),
            80 => 
            array (
                'permission_id' => 37,
                'role_id' => 1,
            ),
            81 => 
            array (
                'permission_id' => 37,
                'role_id' => 3,
            ),
            82 => 
            array (
                'permission_id' => 37,
                'role_id' => 7,
            ),
            83 => 
            array (
                'permission_id' => 38,
                'role_id' => 1,
            ),
            84 => 
            array (
                'permission_id' => 38,
                'role_id' => 3,
            ),
            85 => 
            array (
                'permission_id' => 38,
                'role_id' => 7,
            ),
            86 => 
            array (
                'permission_id' => 39,
                'role_id' => 1,
            ),
            87 => 
            array (
                'permission_id' => 39,
                'role_id' => 7,
            ),
            88 => 
            array (
                'permission_id' => 40,
                'role_id' => 1,
            ),
            89 => 
            array (
                'permission_id' => 40,
                'role_id' => 7,
            ),
            90 => 
            array (
                'permission_id' => 41,
                'role_id' => 1,
            ),
            91 => 
            array (
                'permission_id' => 41,
                'role_id' => 7,
            ),
            92 => 
            array (
                'permission_id' => 42,
                'role_id' => 1,
            ),
            93 => 
            array (
                'permission_id' => 42,
                'role_id' => 3,
            ),
            94 => 
            array (
                'permission_id' => 42,
                'role_id' => 7,
            ),
            95 => 
            array (
                'permission_id' => 43,
                'role_id' => 1,
            ),
            96 => 
            array (
                'permission_id' => 43,
                'role_id' => 3,
            ),
            97 => 
            array (
                'permission_id' => 43,
                'role_id' => 7,
            ),
            98 => 
            array (
                'permission_id' => 44,
                'role_id' => 1,
            ),
            99 => 
            array (
                'permission_id' => 44,
                'role_id' => 7,
            ),
            100 => 
            array (
                'permission_id' => 45,
                'role_id' => 1,
            ),
            101 => 
            array (
                'permission_id' => 45,
                'role_id' => 7,
            ),
            102 => 
            array (
                'permission_id' => 46,
                'role_id' => 1,
            ),
            103 => 
            array (
                'permission_id' => 46,
                'role_id' => 7,
            ),
            104 => 
            array (
                'permission_id' => 47,
                'role_id' => 1,
            ),
            105 => 
            array (
                'permission_id' => 47,
                'role_id' => 3,
            ),
            106 => 
            array (
                'permission_id' => 47,
                'role_id' => 7,
            ),
            107 => 
            array (
                'permission_id' => 48,
                'role_id' => 1,
            ),
            108 => 
            array (
                'permission_id' => 48,
                'role_id' => 3,
            ),
            109 => 
            array (
                'permission_id' => 48,
                'role_id' => 7,
            ),
            110 => 
            array (
                'permission_id' => 49,
                'role_id' => 1,
            ),
            111 => 
            array (
                'permission_id' => 49,
                'role_id' => 7,
            ),
            112 => 
            array (
                'permission_id' => 50,
                'role_id' => 1,
            ),
            113 => 
            array (
                'permission_id' => 50,
                'role_id' => 7,
            ),
            114 => 
            array (
                'permission_id' => 51,
                'role_id' => 1,
            ),
            115 => 
            array (
                'permission_id' => 51,
                'role_id' => 7,
            ),
            116 => 
            array (
                'permission_id' => 52,
                'role_id' => 1,
            ),
            117 => 
            array (
                'permission_id' => 52,
                'role_id' => 3,
            ),
            118 => 
            array (
                'permission_id' => 52,
                'role_id' => 7,
            ),
            119 => 
            array (
                'permission_id' => 53,
                'role_id' => 1,
            ),
            120 => 
            array (
                'permission_id' => 53,
                'role_id' => 3,
            ),
            121 => 
            array (
                'permission_id' => 53,
                'role_id' => 7,
            ),
            122 => 
            array (
                'permission_id' => 54,
                'role_id' => 1,
            ),
            123 => 
            array (
                'permission_id' => 54,
                'role_id' => 7,
            ),
            124 => 
            array (
                'permission_id' => 55,
                'role_id' => 1,
            ),
            125 => 
            array (
                'permission_id' => 55,
                'role_id' => 7,
            ),
            126 => 
            array (
                'permission_id' => 56,
                'role_id' => 1,
            ),
            127 => 
            array (
                'permission_id' => 56,
                'role_id' => 7,
            ),
            128 => 
            array (
                'permission_id' => 57,
                'role_id' => 1,
            ),
            129 => 
            array (
                'permission_id' => 57,
                'role_id' => 3,
            ),
            130 => 
            array (
                'permission_id' => 57,
                'role_id' => 7,
            ),
            131 => 
            array (
                'permission_id' => 58,
                'role_id' => 1,
            ),
            132 => 
            array (
                'permission_id' => 58,
                'role_id' => 3,
            ),
            133 => 
            array (
                'permission_id' => 58,
                'role_id' => 7,
            ),
            134 => 
            array (
                'permission_id' => 59,
                'role_id' => 1,
            ),
            135 => 
            array (
                'permission_id' => 59,
                'role_id' => 7,
            ),
            136 => 
            array (
                'permission_id' => 60,
                'role_id' => 1,
            ),
            137 => 
            array (
                'permission_id' => 60,
                'role_id' => 7,
            ),
            138 => 
            array (
                'permission_id' => 61,
                'role_id' => 1,
            ),
            139 => 
            array (
                'permission_id' => 61,
                'role_id' => 7,
            ),
            140 => 
            array (
                'permission_id' => 62,
                'role_id' => 1,
            ),
            141 => 
            array (
                'permission_id' => 62,
                'role_id' => 3,
            ),
            142 => 
            array (
                'permission_id' => 62,
                'role_id' => 7,
            ),
            143 => 
            array (
                'permission_id' => 63,
                'role_id' => 1,
            ),
            144 => 
            array (
                'permission_id' => 63,
                'role_id' => 3,
            ),
            145 => 
            array (
                'permission_id' => 63,
                'role_id' => 7,
            ),
            146 => 
            array (
                'permission_id' => 64,
                'role_id' => 1,
            ),
            147 => 
            array (
                'permission_id' => 64,
                'role_id' => 7,
            ),
            148 => 
            array (
                'permission_id' => 65,
                'role_id' => 1,
            ),
            149 => 
            array (
                'permission_id' => 65,
                'role_id' => 7,
            ),
            150 => 
            array (
                'permission_id' => 66,
                'role_id' => 1,
            ),
            151 => 
            array (
                'permission_id' => 66,
                'role_id' => 7,
            ),
            152 => 
            array (
                'permission_id' => 67,
                'role_id' => 1,
            ),
            153 => 
            array (
                'permission_id' => 67,
                'role_id' => 3,
            ),
            154 => 
            array (
                'permission_id' => 67,
                'role_id' => 7,
            ),
            155 => 
            array (
                'permission_id' => 68,
                'role_id' => 1,
            ),
            156 => 
            array (
                'permission_id' => 68,
                'role_id' => 3,
            ),
            157 => 
            array (
                'permission_id' => 68,
                'role_id' => 7,
            ),
            158 => 
            array (
                'permission_id' => 69,
                'role_id' => 1,
            ),
            159 => 
            array (
                'permission_id' => 69,
                'role_id' => 7,
            ),
            160 => 
            array (
                'permission_id' => 70,
                'role_id' => 1,
            ),
            161 => 
            array (
                'permission_id' => 70,
                'role_id' => 7,
            ),
            162 => 
            array (
                'permission_id' => 71,
                'role_id' => 1,
            ),
            163 => 
            array (
                'permission_id' => 71,
                'role_id' => 7,
            ),
            164 => 
            array (
                'permission_id' => 72,
                'role_id' => 1,
            ),
            165 => 
            array (
                'permission_id' => 72,
                'role_id' => 3,
            ),
            166 => 
            array (
                'permission_id' => 72,
                'role_id' => 7,
            ),
            167 => 
            array (
                'permission_id' => 73,
                'role_id' => 1,
            ),
            168 => 
            array (
                'permission_id' => 73,
                'role_id' => 3,
            ),
            169 => 
            array (
                'permission_id' => 73,
                'role_id' => 7,
            ),
            170 => 
            array (
                'permission_id' => 74,
                'role_id' => 1,
            ),
            171 => 
            array (
                'permission_id' => 74,
                'role_id' => 7,
            ),
            172 => 
            array (
                'permission_id' => 75,
                'role_id' => 1,
            ),
            173 => 
            array (
                'permission_id' => 75,
                'role_id' => 7,
            ),
            174 => 
            array (
                'permission_id' => 76,
                'role_id' => 1,
            ),
            175 => 
            array (
                'permission_id' => 76,
                'role_id' => 7,
            ),
            176 => 
            array (
                'permission_id' => 77,
                'role_id' => 1,
            ),
            177 => 
            array (
                'permission_id' => 77,
                'role_id' => 3,
            ),
            178 => 
            array (
                'permission_id' => 77,
                'role_id' => 7,
            ),
            179 => 
            array (
                'permission_id' => 78,
                'role_id' => 1,
            ),
            180 => 
            array (
                'permission_id' => 78,
                'role_id' => 3,
            ),
            181 => 
            array (
                'permission_id' => 78,
                'role_id' => 7,
            ),
            182 => 
            array (
                'permission_id' => 79,
                'role_id' => 1,
            ),
            183 => 
            array (
                'permission_id' => 79,
                'role_id' => 7,
            ),
            184 => 
            array (
                'permission_id' => 80,
                'role_id' => 1,
            ),
            185 => 
            array (
                'permission_id' => 80,
                'role_id' => 7,
            ),
            186 => 
            array (
                'permission_id' => 81,
                'role_id' => 1,
            ),
            187 => 
            array (
                'permission_id' => 81,
                'role_id' => 7,
            ),
            188 => 
            array (
                'permission_id' => 82,
                'role_id' => 1,
            ),
            189 => 
            array (
                'permission_id' => 82,
                'role_id' => 3,
            ),
            190 => 
            array (
                'permission_id' => 82,
                'role_id' => 7,
            ),
            191 => 
            array (
                'permission_id' => 83,
                'role_id' => 1,
            ),
            192 => 
            array (
                'permission_id' => 83,
                'role_id' => 3,
            ),
            193 => 
            array (
                'permission_id' => 83,
                'role_id' => 7,
            ),
            194 => 
            array (
                'permission_id' => 84,
                'role_id' => 1,
            ),
            195 => 
            array (
                'permission_id' => 84,
                'role_id' => 7,
            ),
            196 => 
            array (
                'permission_id' => 85,
                'role_id' => 1,
            ),
            197 => 
            array (
                'permission_id' => 85,
                'role_id' => 7,
            ),
            198 => 
            array (
                'permission_id' => 86,
                'role_id' => 1,
            ),
            199 => 
            array (
                'permission_id' => 86,
                'role_id' => 7,
            ),
            200 => 
            array (
                'permission_id' => 87,
                'role_id' => 1,
            ),
            201 => 
            array (
                'permission_id' => 87,
                'role_id' => 3,
            ),
            202 => 
            array (
                'permission_id' => 87,
                'role_id' => 7,
            ),
            203 => 
            array (
                'permission_id' => 88,
                'role_id' => 1,
            ),
            204 => 
            array (
                'permission_id' => 88,
                'role_id' => 3,
            ),
            205 => 
            array (
                'permission_id' => 88,
                'role_id' => 7,
            ),
            206 => 
            array (
                'permission_id' => 89,
                'role_id' => 1,
            ),
            207 => 
            array (
                'permission_id' => 89,
                'role_id' => 7,
            ),
            208 => 
            array (
                'permission_id' => 90,
                'role_id' => 1,
            ),
            209 => 
            array (
                'permission_id' => 90,
                'role_id' => 7,
            ),
            210 => 
            array (
                'permission_id' => 91,
                'role_id' => 1,
            ),
            211 => 
            array (
                'permission_id' => 91,
                'role_id' => 7,
            ),
            212 => 
            array (
                'permission_id' => 92,
                'role_id' => 1,
            ),
            213 => 
            array (
                'permission_id' => 92,
                'role_id' => 3,
            ),
            214 => 
            array (
                'permission_id' => 92,
                'role_id' => 7,
            ),
            215 => 
            array (
                'permission_id' => 93,
                'role_id' => 1,
            ),
            216 => 
            array (
                'permission_id' => 93,
                'role_id' => 7,
            ),
            217 => 
            array (
                'permission_id' => 94,
                'role_id' => 1,
            ),
            218 => 
            array (
                'permission_id' => 94,
                'role_id' => 7,
            ),
            219 => 
            array (
                'permission_id' => 95,
                'role_id' => 1,
            ),
            220 => 
            array (
                'permission_id' => 95,
                'role_id' => 7,
            ),
            221 => 
            array (
                'permission_id' => 96,
                'role_id' => 1,
            ),
            222 => 
            array (
                'permission_id' => 96,
                'role_id' => 7,
            ),
            223 => 
            array (
                'permission_id' => 97,
                'role_id' => 1,
            ),
            224 => 
            array (
                'permission_id' => 97,
                'role_id' => 3,
            ),
            225 => 
            array (
                'permission_id' => 97,
                'role_id' => 7,
            ),
            226 => 
            array (
                'permission_id' => 98,
                'role_id' => 1,
            ),
            227 => 
            array (
                'permission_id' => 98,
                'role_id' => 3,
            ),
            228 => 
            array (
                'permission_id' => 98,
                'role_id' => 7,
            ),
            229 => 
            array (
                'permission_id' => 99,
                'role_id' => 1,
            ),
            230 => 
            array (
                'permission_id' => 99,
                'role_id' => 7,
            ),
            231 => 
            array (
                'permission_id' => 100,
                'role_id' => 1,
            ),
            232 => 
            array (
                'permission_id' => 100,
                'role_id' => 7,
            ),
            233 => 
            array (
                'permission_id' => 101,
                'role_id' => 1,
            ),
            234 => 
            array (
                'permission_id' => 101,
                'role_id' => 7,
            ),
            235 => 
            array (
                'permission_id' => 102,
                'role_id' => 1,
            ),
            236 => 
            array (
                'permission_id' => 102,
                'role_id' => 3,
            ),
            237 => 
            array (
                'permission_id' => 102,
                'role_id' => 7,
            ),
            238 => 
            array (
                'permission_id' => 103,
                'role_id' => 1,
            ),
            239 => 
            array (
                'permission_id' => 103,
                'role_id' => 3,
            ),
            240 => 
            array (
                'permission_id' => 103,
                'role_id' => 7,
            ),
            241 => 
            array (
                'permission_id' => 104,
                'role_id' => 1,
            ),
            242 => 
            array (
                'permission_id' => 104,
                'role_id' => 7,
            ),
            243 => 
            array (
                'permission_id' => 105,
                'role_id' => 1,
            ),
            244 => 
            array (
                'permission_id' => 105,
                'role_id' => 7,
            ),
            245 => 
            array (
                'permission_id' => 106,
                'role_id' => 1,
            ),
            246 => 
            array (
                'permission_id' => 106,
                'role_id' => 7,
            ),
            247 => 
            array (
                'permission_id' => 107,
                'role_id' => 1,
            ),
            248 => 
            array (
                'permission_id' => 107,
                'role_id' => 3,
            ),
            249 => 
            array (
                'permission_id' => 107,
                'role_id' => 7,
            ),
            250 => 
            array (
                'permission_id' => 108,
                'role_id' => 1,
            ),
            251 => 
            array (
                'permission_id' => 108,
                'role_id' => 3,
            ),
            252 => 
            array (
                'permission_id' => 108,
                'role_id' => 7,
            ),
            253 => 
            array (
                'permission_id' => 109,
                'role_id' => 1,
            ),
            254 => 
            array (
                'permission_id' => 109,
                'role_id' => 7,
            ),
            255 => 
            array (
                'permission_id' => 110,
                'role_id' => 1,
            ),
            256 => 
            array (
                'permission_id' => 110,
                'role_id' => 7,
            ),
            257 => 
            array (
                'permission_id' => 111,
                'role_id' => 1,
            ),
            258 => 
            array (
                'permission_id' => 111,
                'role_id' => 7,
            ),
            259 => 
            array (
                'permission_id' => 112,
                'role_id' => 1,
            ),
            260 => 
            array (
                'permission_id' => 112,
                'role_id' => 3,
            ),
            261 => 
            array (
                'permission_id' => 112,
                'role_id' => 7,
            ),
            262 => 
            array (
                'permission_id' => 113,
                'role_id' => 1,
            ),
            263 => 
            array (
                'permission_id' => 113,
                'role_id' => 3,
            ),
            264 => 
            array (
                'permission_id' => 113,
                'role_id' => 7,
            ),
            265 => 
            array (
                'permission_id' => 114,
                'role_id' => 1,
            ),
            266 => 
            array (
                'permission_id' => 114,
                'role_id' => 7,
            ),
            267 => 
            array (
                'permission_id' => 115,
                'role_id' => 1,
            ),
            268 => 
            array (
                'permission_id' => 115,
                'role_id' => 7,
            ),
            269 => 
            array (
                'permission_id' => 116,
                'role_id' => 1,
            ),
            270 => 
            array (
                'permission_id' => 116,
                'role_id' => 7,
            ),
            271 => 
            array (
                'permission_id' => 117,
                'role_id' => 1,
            ),
            272 => 
            array (
                'permission_id' => 117,
                'role_id' => 3,
            ),
            273 => 
            array (
                'permission_id' => 117,
                'role_id' => 7,
            ),
            274 => 
            array (
                'permission_id' => 118,
                'role_id' => 1,
            ),
            275 => 
            array (
                'permission_id' => 118,
                'role_id' => 3,
            ),
            276 => 
            array (
                'permission_id' => 118,
                'role_id' => 7,
            ),
            277 => 
            array (
                'permission_id' => 119,
                'role_id' => 1,
            ),
            278 => 
            array (
                'permission_id' => 119,
                'role_id' => 7,
            ),
            279 => 
            array (
                'permission_id' => 120,
                'role_id' => 1,
            ),
            280 => 
            array (
                'permission_id' => 120,
                'role_id' => 7,
            ),
            281 => 
            array (
                'permission_id' => 121,
                'role_id' => 1,
            ),
            282 => 
            array (
                'permission_id' => 121,
                'role_id' => 7,
            ),
            283 => 
            array (
                'permission_id' => 122,
                'role_id' => 1,
            ),
            284 => 
            array (
                'permission_id' => 122,
                'role_id' => 3,
            ),
            285 => 
            array (
                'permission_id' => 122,
                'role_id' => 7,
            ),
            286 => 
            array (
                'permission_id' => 123,
                'role_id' => 1,
            ),
            287 => 
            array (
                'permission_id' => 123,
                'role_id' => 7,
            ),
            288 => 
            array (
                'permission_id' => 124,
                'role_id' => 1,
            ),
            289 => 
            array (
                'permission_id' => 124,
                'role_id' => 7,
            ),
            290 => 
            array (
                'permission_id' => 125,
                'role_id' => 1,
            ),
            291 => 
            array (
                'permission_id' => 125,
                'role_id' => 7,
            ),
            292 => 
            array (
                'permission_id' => 126,
                'role_id' => 1,
            ),
            293 => 
            array (
                'permission_id' => 126,
                'role_id' => 7,
            ),
            294 => 
            array (
                'permission_id' => 127,
                'role_id' => 1,
            ),
            295 => 
            array (
                'permission_id' => 127,
                'role_id' => 3,
            ),
            296 => 
            array (
                'permission_id' => 127,
                'role_id' => 7,
            ),
            297 => 
            array (
                'permission_id' => 128,
                'role_id' => 1,
            ),
            298 => 
            array (
                'permission_id' => 128,
                'role_id' => 7,
            ),
            299 => 
            array (
                'permission_id' => 129,
                'role_id' => 1,
            ),
            300 => 
            array (
                'permission_id' => 129,
                'role_id' => 7,
            ),
            301 => 
            array (
                'permission_id' => 130,
                'role_id' => 1,
            ),
            302 => 
            array (
                'permission_id' => 130,
                'role_id' => 7,
            ),
            303 => 
            array (
                'permission_id' => 131,
                'role_id' => 1,
            ),
            304 => 
            array (
                'permission_id' => 131,
                'role_id' => 7,
            ),
            305 => 
            array (
                'permission_id' => 132,
                'role_id' => 1,
            ),
            306 => 
            array (
                'permission_id' => 132,
                'role_id' => 3,
            ),
            307 => 
            array (
                'permission_id' => 132,
                'role_id' => 7,
            ),
            308 => 
            array (
                'permission_id' => 133,
                'role_id' => 1,
            ),
            309 => 
            array (
                'permission_id' => 133,
                'role_id' => 3,
            ),
            310 => 
            array (
                'permission_id' => 133,
                'role_id' => 7,
            ),
            311 => 
            array (
                'permission_id' => 134,
                'role_id' => 7,
            ),
            312 => 
            array (
                'permission_id' => 135,
                'role_id' => 7,
            ),
            313 => 
            array (
                'permission_id' => 136,
                'role_id' => 1,
            ),
            314 => 
            array (
                'permission_id' => 136,
                'role_id' => 7,
            ),
            315 => 
            array (
                'permission_id' => 142,
                'role_id' => 1,
            ),
            316 => 
            array (
                'permission_id' => 142,
                'role_id' => 3,
            ),
            317 => 
            array (
                'permission_id' => 142,
                'role_id' => 7,
            ),
            318 => 
            array (
                'permission_id' => 143,
                'role_id' => 1,
            ),
            319 => 
            array (
                'permission_id' => 143,
                'role_id' => 3,
            ),
            320 => 
            array (
                'permission_id' => 143,
                'role_id' => 7,
            ),
            321 => 
            array (
                'permission_id' => 144,
                'role_id' => 1,
            ),
            322 => 
            array (
                'permission_id' => 144,
                'role_id' => 7,
            ),
            323 => 
            array (
                'permission_id' => 145,
                'role_id' => 1,
            ),
            324 => 
            array (
                'permission_id' => 145,
                'role_id' => 7,
            ),
            325 => 
            array (
                'permission_id' => 146,
                'role_id' => 1,
            ),
            326 => 
            array (
                'permission_id' => 146,
                'role_id' => 7,
            ),
            327 => 
            array (
                'permission_id' => 147,
                'role_id' => 1,
            ),
            328 => 
            array (
                'permission_id' => 147,
                'role_id' => 3,
            ),
            329 => 
            array (
                'permission_id' => 147,
                'role_id' => 7,
            ),
            330 => 
            array (
                'permission_id' => 148,
                'role_id' => 1,
            ),
            331 => 
            array (
                'permission_id' => 148,
                'role_id' => 3,
            ),
            332 => 
            array (
                'permission_id' => 148,
                'role_id' => 7,
            ),
            333 => 
            array (
                'permission_id' => 149,
                'role_id' => 1,
            ),
            334 => 
            array (
                'permission_id' => 149,
                'role_id' => 7,
            ),
            335 => 
            array (
                'permission_id' => 150,
                'role_id' => 1,
            ),
            336 => 
            array (
                'permission_id' => 150,
                'role_id' => 7,
            ),
            337 => 
            array (
                'permission_id' => 151,
                'role_id' => 1,
            ),
            338 => 
            array (
                'permission_id' => 151,
                'role_id' => 7,
            ),
            339 => 
            array (
                'permission_id' => 152,
                'role_id' => 1,
            ),
            340 => 
            array (
                'permission_id' => 152,
                'role_id' => 7,
            ),
            341 => 
            array (
                'permission_id' => 153,
                'role_id' => 1,
            ),
            342 => 
            array (
                'permission_id' => 153,
                'role_id' => 7,
            ),
            343 => 
            array (
                'permission_id' => 154,
                'role_id' => 1,
            ),
            344 => 
            array (
                'permission_id' => 154,
                'role_id' => 7,
            ),
            345 => 
            array (
                'permission_id' => 155,
                'role_id' => 1,
            ),
            346 => 
            array (
                'permission_id' => 155,
                'role_id' => 7,
            ),
            347 => 
            array (
                'permission_id' => 156,
                'role_id' => 1,
            ),
            348 => 
            array (
                'permission_id' => 156,
                'role_id' => 7,
            ),
            349 => 
            array (
                'permission_id' => 157,
                'role_id' => 1,
            ),
            350 => 
            array (
                'permission_id' => 157,
                'role_id' => 7,
            ),
            351 => 
            array (
                'permission_id' => 158,
                'role_id' => 1,
            ),
            352 => 
            array (
                'permission_id' => 158,
                'role_id' => 7,
            ),
            353 => 
            array (
                'permission_id' => 159,
                'role_id' => 1,
            ),
            354 => 
            array (
                'permission_id' => 159,
                'role_id' => 7,
            ),
            355 => 
            array (
                'permission_id' => 160,
                'role_id' => 1,
            ),
            356 => 
            array (
                'permission_id' => 160,
                'role_id' => 7,
            ),
            357 => 
            array (
                'permission_id' => 161,
                'role_id' => 1,
            ),
            358 => 
            array (
                'permission_id' => 161,
                'role_id' => 7,
            ),
            359 => 
            array (
                'permission_id' => 162,
                'role_id' => 1,
            ),
            360 => 
            array (
                'permission_id' => 162,
                'role_id' => 7,
            ),
            361 => 
            array (
                'permission_id' => 163,
                'role_id' => 1,
            ),
            362 => 
            array (
                'permission_id' => 163,
                'role_id' => 7,
            ),
            363 => 
            array (
                'permission_id' => 164,
                'role_id' => 1,
            ),
            364 => 
            array (
                'permission_id' => 164,
                'role_id' => 7,
            ),
            365 => 
            array (
                'permission_id' => 165,
                'role_id' => 1,
            ),
            366 => 
            array (
                'permission_id' => 165,
                'role_id' => 7,
            ),
            367 => 
            array (
                'permission_id' => 166,
                'role_id' => 1,
            ),
            368 => 
            array (
                'permission_id' => 166,
                'role_id' => 7,
            ),
            369 => 
            array (
                'permission_id' => 167,
                'role_id' => 1,
            ),
            370 => 
            array (
                'permission_id' => 167,
                'role_id' => 7,
            ),
            371 => 
            array (
                'permission_id' => 168,
                'role_id' => 1,
            ),
            372 => 
            array (
                'permission_id' => 168,
                'role_id' => 7,
            ),
            373 => 
            array (
                'permission_id' => 169,
                'role_id' => 1,
            ),
            374 => 
            array (
                'permission_id' => 169,
                'role_id' => 7,
            ),
            375 => 
            array (
                'permission_id' => 170,
                'role_id' => 1,
            ),
            376 => 
            array (
                'permission_id' => 170,
                'role_id' => 7,
            ),
            377 => 
            array (
                'permission_id' => 171,
                'role_id' => 1,
            ),
            378 => 
            array (
                'permission_id' => 171,
                'role_id' => 7,
            ),
            379 => 
            array (
                'permission_id' => 172,
                'role_id' => 1,
            ),
            380 => 
            array (
                'permission_id' => 172,
                'role_id' => 7,
            ),
            381 => 
            array (
                'permission_id' => 173,
                'role_id' => 1,
            ),
            382 => 
            array (
                'permission_id' => 173,
                'role_id' => 7,
            ),
            383 => 
            array (
                'permission_id' => 174,
                'role_id' => 1,
            ),
            384 => 
            array (
                'permission_id' => 174,
                'role_id' => 7,
            ),
            385 => 
            array (
                'permission_id' => 175,
                'role_id' => 1,
            ),
            386 => 
            array (
                'permission_id' => 175,
                'role_id' => 7,
            ),
            387 => 
            array (
                'permission_id' => 176,
                'role_id' => 1,
            ),
            388 => 
            array (
                'permission_id' => 176,
                'role_id' => 7,
            ),
            389 => 
            array (
                'permission_id' => 177,
                'role_id' => 1,
            ),
            390 => 
            array (
                'permission_id' => 177,
                'role_id' => 7,
            ),
            391 => 
            array (
                'permission_id' => 178,
                'role_id' => 1,
            ),
            392 => 
            array (
                'permission_id' => 178,
                'role_id' => 7,
            ),
            393 => 
            array (
                'permission_id' => 179,
                'role_id' => 1,
            ),
            394 => 
            array (
                'permission_id' => 179,
                'role_id' => 7,
            ),
            395 => 
            array (
                'permission_id' => 180,
                'role_id' => 1,
            ),
            396 => 
            array (
                'permission_id' => 180,
                'role_id' => 7,
            ),
            397 => 
            array (
                'permission_id' => 181,
                'role_id' => 1,
            ),
            398 => 
            array (
                'permission_id' => 181,
                'role_id' => 7,
            ),
            399 => 
            array (
                'permission_id' => 187,
                'role_id' => 1,
            ),
            400 => 
            array (
                'permission_id' => 187,
                'role_id' => 7,
            ),
            401 => 
            array (
                'permission_id' => 188,
                'role_id' => 1,
            ),
            402 => 
            array (
                'permission_id' => 188,
                'role_id' => 7,
            ),
            403 => 
            array (
                'permission_id' => 189,
                'role_id' => 1,
            ),
            404 => 
            array (
                'permission_id' => 189,
                'role_id' => 7,
            ),
            405 => 
            array (
                'permission_id' => 190,
                'role_id' => 1,
            ),
            406 => 
            array (
                'permission_id' => 190,
                'role_id' => 7,
            ),
            407 => 
            array (
                'permission_id' => 191,
                'role_id' => 1,
            ),
            408 => 
            array (
                'permission_id' => 191,
                'role_id' => 7,
            ),
        ));
        
        
    }
}