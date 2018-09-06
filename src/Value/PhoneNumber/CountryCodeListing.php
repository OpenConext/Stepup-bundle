<?php

/**
 * Copyright 2014 SURFnet bv
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Surfnet\StepupBundle\Value\PhoneNumber;

class CountryCodeListing
{
    /**
     * The preferred choice to display on forms. Currently The Netherlands (+31).
     */
    const PREFERRED_CHOICE = '31';

    /**
     * List of currently (2015-03-16) known and used country codes as per
     * {@see en.wikipedia.org/wiki/List_of_country_calling_codes}
     *
     * Due to the fact that a single country can have multiple codes (e.g. Abkhazia) and that a single code
     * can be linked to multiple countries (e.g. '+1' -> US and Canada) we use the formal definition linked to
     * the actual code.
     *
     * When updating, update CountryCodeListing::$countryCodes to match.
     *
     * @var array
     */
    private static $countries = [
        'Abkhazia (+7 840)'                                   => '7840',
        'Abkhazia (+7 940)'                                   => '7940',
        'Afghanistan (+93)'                                   => '93',
        'Albania (+355)'                                      => '355',
        'Algeria (+213)'                                      => '213',
        'American Samoa (+1 684)'                             => '1684',
        'Andorra (+376)'                                      => '376',
        'Angola (+244)'                                       => '244',
        'Anguilla (+1 264)'                                   => '1264',
        'Antigua and Barbuda (+1 268)'                        => '1268',
        'Argentina (+54)'                                     => '54',
        'Armenia (+374)'                                      => '374',
        'Aruba (+297)'                                        => '297',
        'Ascension (+247)'                                    => '247',
        'Australia (+61)'                                     => '61',
        'Australian External Territories (+672)'              => '672',
        'Austria (+43)'                                       => '43',
        'Azerbaijan (+994)'                                   => '994',
        'Bahamas (+1 242)'                                    => '1242',
        'Bahrain (+973)'                                      => '973',
        'Bangladesh (+880)'                                   => '880',
        'Barbados (+1 246)'                                   => '1246',
        'Barbuda (+1 268)'                                    => '1268',
        'Belarus (+375)'                                      => '375',
        'Belgium (+32)'                                       => '32',
        'Belize (+501)'                                       => '501',
        'Benin (+229)'                                        => '229',
        'Bermuda (+1 441)'                                    => '1441',
        'Bhutan (+975)'                                       => '975',
        'Bolivia (+591)'                                      => '591',
        'Bosnia and Herzegovina (+387)'                       => '387',
        'Botswana (+267)'                                     => '267',
        'Brazil (+55)'                                        => '55',
        'British Indian Ocean Territory (+246)'               => '246',
        'British Virgin Islands (+1 284)'                     => '1284',
        'Brunei (+673)'                                       => '673',
        'Bulgaria (+359)'                                     => '359',
        'Burkina Faso (+226)'                                 => '226',
        'Burundi (+257)'                                      => '257',
        'Cambodia (+855)'                                     => '855',
        'Cameroon (+237)'                                     => '237',
        'Canada (+1)'                                         => '1',
        'Cape Verde (+238)'                                   => '238',
        'Cayman Islands (+ 345)'                              => '345',
        'Central African Republic (+236)'                     => '236',
        'Chad (+235)'                                         => '235',
        'Chile (+56)'                                         => '56',
        'China (+86)'                                         => '86',
        'Christmas Island (+61)'                              => '61',
        'Cocos-Keeling Islands (+61)'                         => '61',
        'Colombia (+57)'                                      => '57',
        'Comoros (+269)'                                      => '269',
        'Congo (+242)'                                        => '242',
        'Congo, Dem. Rep. of (Zaire) (+243)'                  => '243',
        'Cook Islands (+682)'                                 => '682',
        'Costa Rica (+506)'                                   => '506',
        'Ivory Coast (+225)'                                  => '225',
        'Croatia (+385)'                                      => '385',
        'Cuba (+53)'                                          => '53',
        'Curacao (+599)'                                      => '599',
        'Cyprus (+537)'                                       => '537',
        'Czech Republic (+420)'                               => '420',
        'Denmark (+45)'                                       => '45',
        'Diego Garcia (+246)'                                 => '246',
        'Djibouti (+253)'                                     => '253',
        'Dominica (+1 767)'                                   => '1767',
        'Dominican Republic (+1 809)'                         => '1809',
        'Dominican Republic (+1 829)'                         => '1829',
        'Dominican Republic (+1 849)'                         => '1849',
        'East Timor (+670)'                                   => '670',
        'Easter Island (+56)'                                 => '56',
        'Ecuador (+593)'                                      => '593',
        'Egypt (+20)'                                         => '20',
        'El Salvador (+503)'                                  => '503',
        'Equatorial Guinea (+240)'                            => '240',
        'Eritrea (+291)'                                      => '291',
        'Estonia (+372)'                                      => '372',
        'Ethiopia (+251)'                                     => '251',
        'Falkland Islands (+500)'                             => '500',
        'Faroe Islands (+298)'                                => '298',
        'Fiji (+679)'                                         => '679',
        'Finland (+358)'                                      => '358',
        'France (+33)'                                        => '33',
        'French Antilles (+596)'                              => '596',
        'French Guiana (+594)'                                => '594',
        'French Polynesia (+689)'                             => '689',
        'Gabon (+241)'                                        => '241',
        'Gambia (+220)'                                       => '220',
        'Georgia (+995)'                                      => '995',
        'Germany (+49)'                                       => '49',
        'Ghana (+233)'                                        => '233',
        'Gibraltar (+350)'                                    => '350',
        'Greece (+30)'                                        => '30',
        'Greenland (+299)'                                    => '299',
        'Grenada (+1 473)'                                    => '1473',
        'Guadeloupe (+590)'                                   => '590',
        'Guam (+1 671)'                                       => '1671',
        'Guatemala (+502)'                                    => '502',
        'Guinea (+224)'                                       => '224',
        'Guinea-Bissau (+245)'                                => '245',
        'Guyana (+595)'                                       => '595',
        'Haiti (+509)'                                        => '509',
        'Honduras (+504)'                                     => '504',
        'Hong Kong SAR China (+852)'                          => '852',
        'Hungary (+36)'                                       => '36',
        'Iceland (+354)'                                      => '354',
        'India (+91)'                                         => '91',
        'Indonesia (+62)'                                     => '62',
        'Iran (+98)'                                          => '98',
        'Iraq (+964)'                                         => '964',
        'Ireland (+353)'                                      => '353',
        'Israel (+972)'                                       => '972',
        'Italy (+39)'                                         => '39',
        'Jamaica (+1 876)'                                    => '1876',
        'Japan (+81)'                                         => '81',
        'Jordan (+962)'                                       => '962',
        'Kazakhstan (+76)'                                    => '76',
        'Kazakhstan (+77)'                                    => '77',
        'Kenya (+254)'                                        => '254',
        'Kiribati (+686)'                                     => '686',
        'North Korea (+850)'                                  => '850',
        'South Korea (+82)'                                   => '82',
        'Kuwait (+965)'                                       => '965',
        'Kyrgyzstan (+996)'                                   => '996',
        'Laos (+856)'                                         => '856',
        'Latvia (+371)'                                       => '371',
        'Lebanon (+961)'                                      => '961',
        'Lesotho (+266)'                                      => '266',
        'Liberia (+231)'                                      => '231',
        'Libya (+218)'                                        => '218',
        'Liechtenstein (+423)'                                => '423',
        'Lithuania (+370)'                                    => '370',
        'Luxembourg (+352)'                                   => '352',
        'Macau SAR China (+853)'                              => '853',
        'Macedonia (+389)'                                    => '389',
        'Madagascar (+261)'                                   => '261',
        'Malawi (+265)'                                       => '265',
        'Malaysia (+60)'                                      => '60',
        'Maldives (+960)'                                     => '960',
        'Mali (+223)'                                         => '223',
        'Malta (+356)'                                        => '356',
        'Marshall Islands (+692)'                             => '692',
        'Martinique (+596)'                                   => '596',
        'Mauritania (+222)'                                   => '222',
        'Mauritius (+230)'                                    => '230',
        'Mayotte (+262)'                                      => '262',
        'Mexico (+52)'                                        => '52',
        'Micronesia (+691)'                                   => '691',
        'Midway Island (+1 808)'                              => '1808',
        'Moldova (+373)'                                      => '373',
        'Monaco (+377)'                                       => '377',
        'Mongolia (+976)'                                     => '976',
        'Montenegro (+382)'                                   => '382',
        'Montserrat (+1664)'                                  => '1664',
        'Morocco (+212)'                                      => '212',
        'Myanmar (+95)'                                       => '95',
        'Namibia (+264)'                                      => '264',
        'Nauru (+674)'                                        => '674',
        'Nepal (+977)'                                        => '977',
        'Netherlands (+31)'                                   => '31',
        'Netherlands Antilles (+599)'                         => '599',
        'Nevis (+1 869)'                                      => '1869',
        'New Caledonia (+687)'                                => '687',
        'New Zealand (64)'                                    => '64',
        'Nicaragua (+505)'                                    => '505',
        'Niger (+227)'                                        => '227',
        'Nigeria (+234)'                                      => '234',
        'Niue (+683)'                                         => '683',
        'Norfolk Island (+672)'                               => '672',
        'Northern Mariana Islands (+1 670)'                   => '1670',
        'Norway (+47)'                                        => '47',
        'Oman (+968)'                                         => '968',
        'Pakistan (+92)'                                      => '92',
        'Palau (+680)'                                        => '680',
        'Palestinian Territory (+970)'                        => '970',
        'Panama (+507)'                                       => '507',
        'Papua New Guinea (+675)'                             => '675',
        'Paraguay (+595)'                                     => '595',
        'Peru (+51)'                                          => '51',
        'Philippines (+63)'                                   => '63',
        'Poland (+48)'                                        => '48',
        'Portugal (+351)'                                     => '351',
        'Puerto Rico (+1 787)'                                => '1787',
        'Puerto Rico (+1 939)'                                => '1939',
        'Qatar (+974)'                                        => '974',
        'Reunion (+262)'                                      => '262',
        'Romania (+40)'                                       => '40',
        'Russia (+7)'                                         => '7',
        'Rwanda (+250)'                                       => '250',
        'Samoa (+685)'                                        => '685',
        'San Marino (+378)'                                   => '378',
        'Saudi Arabia (+966)'                                 => '966',
        'Senegal (+221)'                                      => '221',
        'Serbia (+381)'                                       => '381',
        'Seychelles (+248)'                                   => '248',
        'Sierra Leone (+232)'                                 => '232',
        'Singapore (+65)'                                     => '65',
        'Slovakia (+421)'                                     => '421',
        'Slovenia (+386)'                                     => '386',
        'Solomon Islands (+677)'                              => '677',
        'South Africa (+27)'                                  => '27',
        'South Georgia and the South Sandwich Islands (+500)' => '500',
        'Spain (+34)'                                         => '34',
        'Sri Lanka (+94)'                                     => '94',
        'Sudan (+249)'                                        => '249',
        'Suriname (+597)'                                     => '597',
        'Swaziland (+268)'                                    => '268',
        'Sweden (+46)'                                        => '46',
        'Switzerland (+41)'                                   => '41',
        'Syria (+963)'                                        => '963',
        'Taiwan (+886)'                                       => '886',
        'Tajikistan (+992)'                                   => '992',
        'Tanzania (+255)'                                     => '255',
        'Thailand (+66)'                                      => '66',
        'Timor Leste (+670)'                                  => '670',
        'Togo (+228)'                                         => '228',
        'Tokelau (+690)'                                      => '690',
        'Tonga (+676)'                                        => '676',
        'Trinidad and Tobago (+1 868)'                        => '1868',
        'Tunisia (+216)'                                      => '216',
        'Turkey (+90)'                                        => '90',
        'Turkmenistan (+993)'                                 => '993',
        'Turks and Caicos Islands (+1 649)'                   => '1649',
        'Tuvalu (+688)'                                       => '688',
        'Uganda (+256)'                                       => '256',
        'Ukraine (+380)'                                      => '380',
        'United Arab Emirates (+971)'                         => '971',
        'United Kingdom (+44)'                                => '44',
        'United States (+1)'                                  => '1',
        'Uruguay (+598)'                                      => '598',
        'U.S. Virgin Islands (+1 340)'                        => '1340',
        'Uzbekistan (+998)'                                   => '998',
        'Vanuatu (+678)'                                      => '678',
        'Venezuela (+58)'                                     => '58',
        'Vietnam (+84)'                                       => '84',
        'Wake Island (+1 808)'                                => '1808',
        'Wallis and Futuna (+681)'                            => '681',
        'Yemen (+967)'                                        => '967',
        'Zambia (+260)'                                       => '260',
        'Zanzibar (+255)'                                     => '255',
        'Zimbabwe (+263)'                                     => '263',
    ];

    public static function asArray()
    {
        $countries = [];

        foreach (self::$countries as $name => $code) {
            $countries[] = new Country(new CountryCode($code), $name);
        }

        $countryNames = array_keys(self::$countries);

        return array_combine($countryNames, $countries);
    }
    /**
     * @param Country $country
     * @return bool
     */
    public static function isPreferredChoice(Country $country)
    {
        return $country->getCountryCode()->equals(new CountryCode(self::PREFERRED_CHOICE));
    }

    /**
     * @param string $countryCode
     * @return bool
     */
    public static function isValidCountryCode($countryCode)
    {
        return in_array($countryCode, self::$countries);
    }
}
