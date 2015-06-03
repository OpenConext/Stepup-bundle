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

use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;

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
     * @var array
     */
    private static $countries = [
        ['7840', 'Abkhazia'],
        ['7940', 'Abkhazia'],
        ['93', 'Afghanistan'],
        ['355', 'Albania'],
        ['213', 'Algeria'],
        ['1684', 'American Samoa'],
        ['376', 'Andorra'],
        ['244', 'Angola'],
        ['1264', 'Anguilla'],
        ['1268', 'Antigua and Barbuda'],
        ['54', 'Argentina'],
        ['374', 'Armenia'],
        ['297', 'Aruba'],
        ['247', 'Ascension'],
        ['61', 'Australia'],
        ['672', 'Australian External Territories'],
        ['43', 'Austria'],
        ['994', 'Azerbaijan'],
        ['1242', 'Bahamas'],
        ['973', 'Bahrain'],
        ['880', 'Bangladesh'],
        ['1246', 'Barbados'],
        ['1268', 'Barbuda'],
        ['375', 'Belarus'],
        ['32', 'Belgium'],
        ['501', 'Belize'],
        ['229', 'Benin'],
        ['1441', 'Bermuda'],
        ['975', 'Bhutan'],
        ['591', 'Bolivia'],
        ['387', 'Bosnia and Herzegovina'],
        ['267', 'Botswana'],
        ['55', 'Brazil'],
        ['246', 'British Indian Ocean Territory'],
        ['1284', 'British Virgin Islands'],
        ['673', 'Brunei'],
        ['359', 'Bulgaria'],
        ['226', 'Burkina Faso'],
        ['257', 'Burundi'],
        ['855', 'Cambodia'],
        ['237', 'Cameroon'],
        ['1', 'Canada'],
        ['238', 'Cape Verde'],
        ['345', 'Cayman Islands'],
        ['236', 'Central African Republic'],
        ['235', 'Chad'],
        ['56', 'Chile'],
        ['86', 'China'],
        ['61', 'Christmas Island'],
        ['61', 'Cocos-Keeling Islands'],
        ['57', 'Colombia'],
        ['269', 'Comoros'],
        ['242', 'Congo'],
        ['243', 'Congo, Dem. Rep. of (Zaire)'],
        ['682', 'Cook Islands'],
        ['506', 'Costa Rica'],
        ['225', 'Ivory Coast'],
        ['385', 'Croatia'],
        ['53', 'Cuba'],
        ['599', 'Curacao'],
        ['537', 'Cyprus'],
        ['420', 'Czech Republic'],
        ['45', 'Denmark'],
        ['246', 'Diego Garcia'],
        ['253', 'Djibouti'],
        ['1767', 'Dominica'],
        ['1809', 'Dominican Republic'],
        ['1829', 'Dominican Republic'],
        ['1849', 'Dominican Republic'],
        ['670', 'East Timor'],
        ['56', 'Easter Island'],
        ['593', 'Ecuador'],
        ['20', 'Egypt'],
        ['503', 'El Salvador'],
        ['240', 'Equatorial Guinea'],
        ['291', 'Eritrea'],
        ['372', 'Estonia'],
        ['251', 'Ethiopia'],
        ['500', 'Falkland Islands'],
        ['298', 'Faroe Islands'],
        ['679', 'Fiji'],
        ['358', 'Finland'],
        ['33', 'France'],
        ['596', 'French Antilles'],
        ['594', 'French Guiana'],
        ['689', 'French Polynesia'],
        ['241', 'Gabon'],
        ['220', 'Gambia'],
        ['995', 'Georgia'],
        ['49', 'Germany'],
        ['233', 'Ghana'],
        ['350', 'Gibraltar'],
        ['30', 'Greece'],
        ['299', 'Greenland'],
        ['1473', 'Grenada'],
        ['590', 'Guadeloupe'],
        ['1671', 'Guam'],
        ['502', 'Guatemala'],
        ['224', 'Guinea'],
        ['245', 'Guinea-Bissau'],
        ['595', 'Guyana'],
        ['509', 'Haiti'],
        ['504', 'Honduras'],
        ['852', 'Hong Kong SAR China'],
        ['36', 'Hungary'],
        ['354', 'Iceland'],
        ['91', 'India'],
        ['62', 'Indonesia'],
        ['98', 'Iran'],
        ['964', 'Iraq'],
        ['353', 'Ireland'],
        ['972', 'Israel'],
        ['39', 'Italy'],
        ['1876', 'Jamaica'],
        ['81', 'Japan'],
        ['962', 'Jordan'],
        ['76', 'Kazakhstan'],
        ['77', 'Kazakhstan'],
        ['254', 'Kenya'],
        ['686', 'Kiribati'],
        ['850', 'North Korea'],
        ['82', 'South Korea'],
        ['965', 'Kuwait'],
        ['996', 'Kyrgyzstan'],
        ['856', 'Laos'],
        ['371', 'Latvia'],
        ['961', 'Lebanon'],
        ['266', 'Lesotho'],
        ['231', 'Liberia'],
        ['218', 'Libya'],
        ['423', 'Liechtenstein'],
        ['370', 'Lithuania'],
        ['352', 'Luxembourg'],
        ['853', 'Macau SAR China'],
        ['389', 'Macedonia'],
        ['261', 'Madagascar'],
        ['265', 'Malawi'],
        ['60', 'Malaysia'],
        ['960', 'Maldives'],
        ['223', 'Mali'],
        ['356', 'Malta'],
        ['692', 'Marshall Islands'],
        ['596', 'Martinique'],
        ['222', 'Mauritania'],
        ['230', 'Mauritius'],
        ['262', 'Mayotte'],
        ['52', 'Mexico'],
        ['691', 'Micronesia'],
        ['1808', 'Midway Island'],
        ['373', 'Moldova'],
        ['377', 'Monaco'],
        ['976', 'Mongolia'],
        ['382', 'Montenegro'],
        ['1664', 'Montserrat'],
        ['212', 'Morocco'],
        ['95', 'Myanmar'],
        ['264', 'Namibia'],
        ['674', 'Nauru'],
        ['977', 'Nepal'],
        ['31', 'Netherlands'],
        ['599', 'Netherlands Antilles'],
        ['1869', 'Nevis'],
        ['687', 'New Caledonia'],
        ['64', 'New Zealand'],
        ['505', 'Nicaragua'],
        ['227', 'Niger'],
        ['234', 'Nigeria'],
        ['683', 'Niue'],
        ['672', 'Norfolk Island'],
        ['1670', 'Northern Mariana Islands'],
        ['47', 'Norway'],
        ['968', 'Oman'],
        ['92', 'Pakistan'],
        ['680', 'Palau'],
        ['970', 'Palestinian Territory'],
        ['507', 'Panama'],
        ['675', 'Papua New Guinea'],
        ['595', 'Paraguay'],
        ['51', 'Peru'],
        ['63', 'Philippines'],
        ['48', 'Poland'],
        ['351', 'Portugal'],
        ['1787', 'Puerto Rico'],
        ['1939', 'Puerto Rico'],
        ['974', 'Qatar'],
        ['262', 'Reunion'],
        ['40', 'Romania'],
        ['7', 'Russia'],
        ['250', 'Rwanda'],
        ['685', 'Samoa'],
        ['378', 'San Marino'],
        ['966', 'Saudi Arabia'],
        ['221', 'Senegal'],
        ['381', 'Serbia'],
        ['248', 'Seychelles'],
        ['232', 'Sierra Leone'],
        ['65', 'Singapore'],
        ['421', 'Slovakia'],
        ['386', 'Slovenia'],
        ['677', 'Solomon Islands'],
        ['27', 'South Africa'],
        ['500', 'South Georgia and the South Sandwich Islands'],
        ['34', 'Spain'],
        ['94', 'Sri Lanka'],
        ['249', 'Sudan'],
        ['597', 'Suriname'],
        ['268', 'Swaziland'],
        ['46', 'Sweden'],
        ['41', 'Switzerland'],
        ['963', 'Syria'],
        ['886', 'Taiwan'],
        ['992', 'Tajikistan'],
        ['255', 'Tanzania'],
        ['66', 'Thailand'],
        ['670', 'Timor Leste'],
        ['228', 'Togo'],
        ['690', 'Tokelau'],
        ['676', 'Tonga'],
        ['1868', 'Trinidad and Tobago'],
        ['216', 'Tunisia'],
        ['90', 'Turkey'],
        ['993', 'Turkmenistan'],
        ['1649', 'Turks and Caicos Islands'],
        ['688', 'Tuvalu'],
        ['256', 'Uganda'],
        ['380', 'Ukraine'],
        ['971', 'United Arab Emirates'],
        ['44', 'United Kingdom'],
        ['1', 'United States'],
        ['598', 'Uruguay'],
        ['1340', 'U.S. Virgin Islands'],
        ['998', 'Uzbekistan'],
        ['678', 'Vanuatu'],
        ['58', 'Venezuela'],
        ['84', 'Vietnam'],
        ['1808', 'Wake Island'],
        ['681', 'Wallis and Futuna'],
        ['967', 'Yemen'],
        ['260', 'Zambia'],
        ['255', 'Zanzibar'],
        ['263', 'Zimbabwe'],
    ];

    /**
     * @return ChoiceListInterface
     */
    public static function asChoiceList()
    {
        $countries = array_map(
            function (array $country) {
                return new Country(new CountryCode($country[0]), $country[1]);
            },
            static::$countries
        );
        $countryNames = array_map('strval', $countries);

        return new ArrayChoiceList(array_combine($countryNames, $countries));
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
        foreach (static::$countries as $country) {
            if ($country[0] == $countryCode) {
                return true;
            }
        }

        return false;
    }
}
