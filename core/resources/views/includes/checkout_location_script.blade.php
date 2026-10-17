{{--
    Shared Province -> City -> Area logic for checkout address forms.
    Expected markup (inside #checkoutBilling):
      #checkout-province (select, name="bill_province")
      #checkout-city (hidden, name="bill_city"), #checkout-city-select, #checkout-city-custom, #checkout-city-back-link
      #checkout-area (hidden, name="bill_area"), #checkout-area-select, #checkout-area-custom, #checkout-area-back-link
    Fires a "checkout:address-changed" event on document whenever city/area values change.
--}}
<style>
    #checkout-city-select option.checkout-manual-option,
    #checkout-area-select option.checkout-manual-option {
        color: #2563eb;
        font-weight: 700;
        background-color: #eff6ff;
    }
    #checkout-city-custom,
    #checkout-area-custom {
        border-color: #2563eb;
    }
    .checkout-back-link {
        font-size: 12px;
        color: #2563eb !important;
        font-weight: 600;
        text-decoration: underline;
        cursor: pointer;
    }
</style>

<script>
    (function() {
        var MANUAL = '__manual__';
        var CITY_MANUAL_LABEL = '+ Enter City Name (Not in list)';
        var AREA_MANUAL_LABEL = '+ Enter Area Name (Not in list)';

        var pakistanProvinceCities = {
            "Azad Kashmir": [
                "Abbaspur", "Athmuqam", "Bagh", "Barnala", "Bhimber", "Chakswari", "Charhoi", "Chikar", "Chinari",
                "Dadyal", "Dhirkot", "Forward Kahuta", "Garhi Dupatta", "Hajira", "Hattian Bala", "Haveli",
                "Islamgarh", "Kahori", "Kel", "Khuiratta", "Kotli", "Leepa", "Mangla", "Mirpur", "Muzaffarabad",
                "Nakiyal", "Neelam", "Pallandri", "Paniola", "Patika", "Rawalakot", "Samahni", "Sehnsa", "Sharda",
                "Sudhanoti", "Tarar Khal", "Thorar"
            ],
            "Balochistan": [
                "Awaran", "Barkhan", "Bela", "Bhag", "Buleda", "Chagai", "Chaman", "Chattar", "Dalbandin",
                "Dera Allah Yar", "Dera Bugti", "Dera Murad Jamali", "Dhadar", "Duki", "Gadani", "Gandawah",
                "Gulistan", "Gwadar", "Harnai", "Hub", "Jafarabad", "Jhal Magsi", "Jiwani", "Kachhi", "Kalat",
                "Khanozai", "Kharan", "Khuzdar", "Killa Abdullah", "Killa Saifullah", "Kohlu", "Lasbela", "Lehri",
                "Loralai", "Mach", "Mand", "Mastung", "Mekhtar", "Musakhel", "Muslim Bagh", "Nasirabad", "Nokkundi",
                "Nushki", "Ormara", "Panjgur", "Pasni", "Pishin", "Qila Abdullah", "Qila Saifullah", "Quetta",
                "Rakhni", "Rojhan Jamali", "Saranan", "Sherani", "Sibi", "Sohbatpur", "Sui", "Surab", "Taftan",
                "Tump", "Turbat", "Usta Muhammad", "Uthal", "Wadh", "Washuk", "Winder", "Zehri", "Zhob", "Ziarat"
            ],
            "Federally Administered Tribal Areas": [
                "Alizai", "Bajaur", "Bara", "Darra Adam Khel", "Dogar", "Fort Lockhart", "Ghalanai", "Ghulam Khan",
                "Hassan Khel", "Inayat Qilla", "Jamrud", "Jandola", "Kalaya", "Khar", "Kharlachi", "Khyber",
                "Kurram", "Landi Kotal", "Makeen", "Mamund", "Mir Ali", "Miranshah", "Mohmand", "Nawagai",
                "North Waziristan", "Orakzai", "Parachinar", "Razmak", "Sadda", "Salarzai", "Sararogha", "Sarwakai",
                "Shakai", "South Waziristan", "Spinwam", "Tiarza", "Torkham", "Wana"
            ],
            "Gilgit-Baltistan": [
                "Aliabad", "Astore", "Bunji", "Chilas", "Dambudas", "Danyor", "Darel", "Diamer", "Gahkuch",
                "Ghanche", "Ghizer", "Gilgit", "Gojal", "Gorikot", "Gultari", "Gupis", "Haldi", "Hopar", "Hunza",
                "Ishkoman", "Jaglot", "Jalalabad", "Jutal", "Karimabad", "Khaplu", "Kharmang", "Minimarg", "Misgar",
                "Nagar", "Naltar", "Nasirabad", "Passu", "Phander", "Roundu", "Shigar", "Shimshal", "Skardu",
                "Sost", "Tangir", "Tolti", "Yasin"
            ],
            "Islamabad": [
                "Bani Gala", "Bhara Kahu", "Chak Shahzad", "DHA Islamabad", "Golra Sharif", "Humak", "Islamabad",
                "Koral", "Loi Bher", "Margalla Town", "Model Town Humak", "Nilore", "Park View City Islamabad",
                "PWD Islamabad", "Rawat", "Sangjani", "Shah Allah Ditta", "Sihala", "Soan Garden", "Tarlai Kalan",
                "Tarnol"
            ],
            "Khyber Pakhtunkhwa": [
                "Abbottabad", "Akora Khattak", "Alpuri", "Amangarh", "Ayubia", "Baffa", "Bahrain", "Balakot",
                "Bannu", "Barikot", "Batagram", "Batkhela", "Besham", "Buner", "Chakdara", "Charsadda", "Cherat",
                "Chitral", "Daggar", "Daraban", "Dargai", "Dasu", "Dera Ismail Khan", "Dir", "Doaba", "Domel",
                "Drosh", "Gadoon Amazai", "Garhi Habibullah", "Garhi Kapura", "Ghazi", "Ghoriwala", "Gulabad",
                "Hangu", "Haripur", "Havelian", "Hayatabad", "Jahangira", "Jalozai", "Kabal", "Kakul", "Kalam",
                "Karak", "Katlang", "Khalabat", "Khawazakhela", "Kohat", "Kohistan", "Kot Najibullah", "Kulachi",
                "Lachi", "Lakki Marwat", "Lamber", "Lower Dir", "Malakand", "Malam Jabba", "Mansehra", "Mardan",
                "Mastuj", "Matta", "Mingora", "Naran", "Nathia Gali", "Naurang", "Nowshera", "Oghi", "Pabbi",
                "Paharpur", "Paniala", "Paroa", "Pattan", "Peshawar", "Pezu", "Puran", "Qalandarabad", "Risalpur",
                "Rustam", "Saidu Sharif", "Samar Bagh", "Serai Naurang", "Shabqadar", "Shahbaz Garhi", "Shaidu",
                "Shangla", "Shewa Adda", "Shinkiari", "Swabi", "Swat", "Takht-i-Bahi", "Tall", "Tangi", "Tank",
                "Tarbela", "Thakot", "Timergara", "Topi", "Tordher", "Torghar", "Totalai", "Upper Dir", "Utmanzai",
                "Wari", "Zaida"
            ],
            "Punjab": [
                "Abdul Hakim", "Ahmadpur East", "Ahmadpur Sial", "Alipur", "Alipur Chattha", "Arifwala", "Attock",
                "Baddomalhi", "Bahawalnagar", "Bahawalpur", "Bhakkar", "Bhalwal", "Bhawana", "Bhera", "Bhurban",
                "Burewala", "Chak Jhumra", "Chakwal", "Chawinda", "Chichawatni", "Chiniot", "Chishtian",
                "Choa Saidanshah", "Chuhar Kana", "Chunian", "Dajal", "Darya Khan", "Daska", "Daud Khel",
                "Depalpur", "Dera Ghazi Khan", "Dhaunkal", "Dijkot", "Dina", "Dinga", "Dunyapur", "Eminabad",
                "Faisalabad", "Faqirwali", "Farooqabad", "Fateh Jang", "Fatehpur", "Fazilpur", "Ferozewala",
                "Fort Abbas", "Gaggoo Mandi", "Garh Maharaja", "Ghakhar Mandi", "Gojra", "Gujar Khan",
                "Gujranwala", "Gujrat", "Hadali", "Hafizabad", "Haroonabad", "Hasilpur", "Hassan Abdal",
                "Haveli Lakha", "Hazro", "Hujra Shah Muqeem", "Isa Khel", "Jahanian", "Jalalpur Bhattian",
                "Jalalpur Jattan", "Jalalpur Pirwala", "Jampur", "Jand", "Jaranwala", "Jatoi", "Jauharabad",
                "Jhang", "Jhelum", "Kabirwala", "Kahna Nau", "Kahror Pakka", "Kahuta", "Kala Bagh",
                "Kala Shah Kaku", "Kallar Kahar", "Kallar Syedan", "Kalur Kot", "Kamalia", "Kamoke", "Kamra",
                "Kanganpur", "Karor Lal Esan", "Kasur", "Khairpur Tamewali", "Khanewal", "Khangah Dogran",
                "Khangarh", "Khanpur", "Khanqah Sharif", "Kharian", "Khewra", "Khurrianwala", "Khushab",
                "Kot Addu", "Kot Chutta", "Kot Mithan", "Kot Momin", "Kot Radha Kishan", "Kot Samaba",
                "Kot Sultan", "Kotli Loharan", "Kotli Sattian", "Kundian", "Kunjah", "Ladhewala Waraich",
                "Lahore", "Lalamusa", "Lalian", "Layyah", "Liaquatpur", "Lodhran", "Luddan", "Mailsi", "Malakwal",
                "Mamukanjan", "Mananwala", "Mandi Bahauddin", "Mandi Faizabad", "Mangla", "Mankera", "Mian Channu",
                "Miani", "Mianwali", "Minchinabad", "Mithankot", "More Khunda", "Multan", "Muridke", "Murree",
                "Mustafabad", "Muzaffargarh", "Nankana Sahib", "Narang Mandi", "Narowal", "Naushera Virkan",
                "Noorpur Thal", "Okara", "Pakpattan", "Pasrur", "Pattoki", "Phalia", "Phularwan", "Pind Dadan Khan",
                "Pindi Bhattian", "Pindi Gheb", "Piplan", "Pir Mahal", "Qadirpur Ran", "Qila Didar Singh",
                "Quaidabad", "Rabwah (Chenab Nagar)", "Rahim Yar Khan", "Raiwind", "Raja Jang", "Rajanpur",
                "Rawalpindi", "Renala Khurd", "Rojhan", "Sadiqabad", "Safdarabad", "Sahiwal", "Sambrial", "Samundri",
                "Sangla Hill", "Sarai Alamgir", "Sargodha", "Shahdara", "Shahkot", "Shahpur", "Shakargarh",
                "Sharaqpur", "Sheikhupura", "Shorkot", "Shujaabad", "Sialkot", "Sillanwali", "Sohawa", "Talagang",
                "Tandlianwala", "Taunsa Sharif", "Taxila", "Tibba Sultanpur", "Toba Tek Singh", "Tulamba",
                "Uch Sharif", "Vehari", "Wah Cantt", "Wan Radha Ram", "Warburton", "Wazirabad", "Yazman",
                "Zafarwal", "Zahir Pir"
            ],
            "Sindh": [
                "Adilpur", "Badah", "Badin", "Bagerji", "Bakrani", "Berani", "Bhan Syedabad", "Bhiria City",
                "Bhiria Road", "Bhit Shah", "Bozdar Wada", "Bulri Shah Karim", "Chachro", "Chamber",
                "Chohar Jamali", "Chor", "Dadu", "Daharki", "Darbelo", "Daro", "Daulatpur", "Daur", "Digri",
                "Diplo", "Dokri", "Faiz Ganj", "Gambat", "Garhi Khairo", "Garhi Yasin", "Gharo", "Ghotki",
                "Golarchi", "Guddu", "Hala", "Halani", "Hingorja", "Hyderabad", "Islamkot", "Jacobabad",
                "Jam Nawaz Ali", "Jamshoro", "Jati", "Jhol", "Jhudo", "Johi", "Kadhan", "Kandhkot", "Kandhra",
                "Kandiaro", "Karachi", "Karampur", "Kario Ghanwar", "Kashmore", "Keti Bandar", "Khairpur",
                "Khairpur Nathan Shah", "Khanpur Mahar", "Khipro", "Khuhra", "Kingri", "Kot Diji",
                "Kot Ghulam Muhammad", "Kotri", "Kunri", "Lakhi Ghulam Shah", "Larkana", "Madeji", "Malir",
                "Manjhand", "Matiari", "Matli", "Mehar", "Mehrabpur", "Mian Sahib", "Mirpur Bathoro",
                "Mirpur Khas", "Mirpur Mathelo", "Mirpur Sakro", "Mirwah", "Mithi", "Moro", "Nagarparkar",
                "Nasirabad", "Naudero", "Naukot", "Naushahro Feroze", "Nawabshah (Shaheed Benazirabad)",
                "New Saeedabad", "Nooriabad", "Odero Lal", "Pacca Chang", "Padidan", "Pano Aqil", "Perumal",
                "Phulji", "Pir Jo Goth", "Piryaloi", "Pithoro", "Qambar (Kamber)", "Qazi Ahmad",
                "Qubo Saeed Khan", "Radhan", "Rahimabad", "Rajo Khanani", "Ranipur", "Ratodero", "Rohri",
                "Rustam", "Saeedabad", "Sakrand", "Samaro", "Sanghar", "Sann", "Sarhari", "Sehwan Sharif",
                "Setharja", "Shahdadkot", "Shahdadpur", "Shahpur Chakar", "Shahpur Jahania", "Shikarpur",
                "Sinjhoro", "Sita Road", "Sobhodero", "Sujawal", "Sukkur", "Talhar", "Tando Adam",
                "Tando Allahyar", "Tando Bago", "Tando Ghulam Ali", "Tando Jam", "Tando Jan Mohammad",
                "Tando Masti", "Tando Muhammad Khan", "Tangwani", "Thari Mirwah", "Tharo Shah", "Thatta",
                "Therhi", "Thul", "Ubauro", "Umerkot", "Warah"
            ]
        };

        // Well-known areas / sectors / housing societies of major cities.
        // Cities not listed here go straight to manual area entry.
        var pakistanCityAreas = {
            "Abbottabad": [
                "Bilal Town", "Cantt", "Dhamtour", "Fawara Chowk", "Habibullah Colony", "Jhangi", "Jinnahabad",
                "Kakul", "Kehal", "Link Road", "Main Bazaar", "Malikpura", "Mandian", "Mirpur", "Murree Road",
                "Nawan Shehr", "PMA Road", "Sheikh-ul-Bandi", "Supply"
            ],
            "Bahawalpur": [
                "Abbasia Town", "Ahmedpur Road", "Baghdad-ul-Jadeed", "Cantt", "DHA Bahawalpur", "Fareed Gate",
                "Hasilpur Road", "Islamic Colony", "Model Town A", "Model Town B", "Model Town C", "Satellite Town",
                "Shahi Bazaar", "Trust Colony", "Yazman Road"
            ],
            "Faisalabad": [
                "Abdullah Garden", "Allama Iqbal Colony", "Batala Colony", "Canal Road", "Citi Housing Faisalabad",
                "Civil Lines", "D Ground", "Eden Valley", "Faisal Town", "Ghulam Muhammad Abad", "Gulberg",
                "Gulistan Colony", "Jaranwala Road", "Jhang Road", "Jinnah Colony", "Khayaban Colony",
                "Kohinoor City", "Madina Town", "Mansoorabad", "Millat Town", "Muslim Town", "Narwala Road",
                "Nishatabad", "Peoples Colony", "Raza Abad", "Saeed Colony", "Samanabad", "Sargodha Road",
                "Satiana Road", "Sheikhupura Road", "Susan Road", "Tariqabad", "Wapda City"
            ],
            "Gujranwala": [
                "Citi Housing Gujranwala", "Civil Lines", "DC Colony", "Eminabad Road", "Garden Town", "Gondlanwala",
                "GT Road", "Khiali", "Master City", "Model Town", "Muslim Town", "Nowshera Road", "Pasrur Road",
                "Peoples Colony", "Rahwali Cantt", "Rachna Town", "Satellite Town", "Sheikhupura Road",
                "Sialkot Bypass", "Wapda Town"
            ],
            "Hyderabad": [
                "Autobhan Road", "Bhitai Town", "Cantonment", "Citizen Colony", "Gari Khata", "Gulistan-e-Sajjad",
                "Hirabad", "Hussainabad", "Jamshoro Road", "Kohsar", "Latifabad", "Liaquat Colony", "Naseem Nagar",
                "Old City", "Paretabad", "Qasimabad", "Railway Colony", "Saddar", "Shahi Bazaar", "SITE Area",
                "Tilak Incline", "Wahdat Colony"
            ],
            "Islamabad": [
                "B-17 (Multi Gardens)", "Bahria Enclave", "Bahria Town Islamabad", "Bani Gala", "Bhara Kahu",
                "Blue Area", "Chak Shahzad", "D-12", "D-17", "DHA Phase 1", "DHA Phase 2", "Diplomatic Enclave",
                "E-7", "E-8", "E-9", "E-11", "F-5", "F-6", "F-7", "F-8", "F-10", "F-11", "F-15", "F-17", "G-5",
                "G-6", "G-7", "G-8", "G-9", "G-10", "G-11", "G-12", "G-13", "G-14", "G-15", "Ghauri Town",
                "Golra Sharif", "Gulberg Greens", "Gulberg Residencia", "H-8", "H-9", "H-11", "H-13", "I-8", "I-9",
                "I-10", "I-11", "I-14", "I-16", "Jinnah Garden", "Korang Town", "Kuri Road", "Lehtrar Road",
                "Margalla Town", "Media Town", "National Police Foundation (O-9)", "Naval Anchorage",
                "Pakistan Town", "Park Road", "PWD Housing Society", "Shahzad Town", "Sihala", "Soan Garden",
                "Tarlai", "Tarnol", "Top City-1", "Zone 5"
            ],
            "Karachi": [
                "Abul Hassan Isphahani Road", "Ancholi", "Azizabad", "Bahadurabad", "Bahria Town Karachi",
                "Baldia Town", "Bath Island", "Bin Qasim Town", "Boat Basin", "Buffer Zone", "Clifton",
                "DHA City Karachi", "DHA Defence", "Federal B Area", "Gadap Town", "Garden East", "Garden West",
                "Gizri", "Gulberg Town", "Gulistan-e-Jauhar", "Gulshan-e-Iqbal", "Gulshan-e-Maymar",
                "Gulzar-e-Hijri", "Jamshed Road", "Kemari", "Korangi", "Korangi Industrial Area", "Landhi",
                "Liaquatabad", "Lyari", "Malir", "Malir Cantt", "Manghopir", "Mehmoodabad", "Model Colony",
                "Nazimabad", "New Karachi", "North Karachi", "North Nazimabad", "Orangi Town", "PECHS",
                "Qayyumabad", "Saddar", "Safoora Goth", "Scheme 33", "Shah Faisal Colony", "Shahrah-e-Faisal",
                "SITE Area", "Soldier Bazaar", "Surjani Town", "Tariq Road", "University Road"
            ],
            "Lahore": [
                "Al-Rehman Garden", "Allama Iqbal Town", "Anarkali", "Askari", "Baghbanpura", "Bahria Orchard",
                "Bahria Town Lahore", "Bedian Road", "Bilal Ganj", "Canal View", "Cantt", "Cavalry Ground",
                "Central Park", "DHA Defence", "EME Society", "Etihad Town", "Faisal Town", "Ferozepur Road",
                "Garden Town", "Ghaziabad", "Green Town", "Gulberg", "Gulshan-e-Lahore", "Gulshan-e-Ravi",
                "Harbanspura", "Ichhra", "Johar Town", "Jubilee Town", "Kahna", "Lake City",
                "Lahore Medical Housing Society", "Model Town", "Mughalpura", "Multan Road", "Muslim Town",
                "Mustafa Town", "Nishtar Colony", "Paragon City", "Park View City Lahore", "Raiwind Road",
                "Revenue Society", "Sabzazar", "Salamatpura", "Samanabad", "Shadman", "Shahdara", "Shalimar",
                "State Life Society", "Sui Gas Society", "Thokar Niaz Baig", "Township", "Valencia Town",
                "Wahdat Colony", "Walled City", "Wapda Town"
            ],
            "Mardan": [
                "Bagh-e-Iram", "Bank Road", "Cantt", "Charsadda Road", "Gujjar Garhi", "Hoti", "Malakand Road",
                "Nowshera Road", "Par Hoti", "Sheikh Maltoon Town", "Swabi Road", "Takht Bhai Road"
            ],
            "Multan": [
                "Bahadurpur", "Bosan Road", "Buch Villas", "Cantt", "Chungi No. 9", "DHA Multan", "Garden Town",
                "Gulgasht Colony", "Hussain Agahi", "Khanewal Road", "Mattital Road", "Model Town", "Mumtazabad",
                "New Multan", "Nishtar Road", "Northern Bypass", "Officers Colony", "Old Shujabad Road",
                "Qasim Bela", "Royal Orchard", "Shah Rukn-e-Alam Colony", "Sher Shah Road", "Vehari Road",
                "Wapda Town"
            ],
            "Peshawar": [
                "Arbab Road", "Bara Road", "Board Bazaar", "Cantt", "Charsadda Road", "Dabgari Gardens",
                "Dalazak Road", "DHA Peshawar", "Faqirabad", "Gulbahar", "Hashtnagri", "Hayatabad", "Kakshal",
                "Khyber Bazaar", "Kohat Road", "Nothia", "Pajagi Road", "Palosi", "Qissa Khwani Bazaar",
                "Regi Model Town", "Ring Road", "Saddar", "Shami Road", "Sufaid Dheri", "Tehkal",
                "University Town", "Warsak Road", "Wazir Bagh", "Yakatoot"
            ],
            "Quetta": [
                "Airport Road", "Arbab Karam Khan Road", "Brewery Road", "Cantt", "Chaman Housing Scheme",
                "Chiltan Housing Scheme", "Hazara Town", "Jinnah Road", "Jinnah Town", "Kasi Road", "Kuchlak Road",
                "Liaquat Bazaar", "Marriabad", "Mission Road", "Model Town", "Nawa Killi", "Pashtunabad",
                "Prince Road", "Quarry Road", "Samungli Road", "Sariab Road", "Satellite Town", "Shahbaz Town",
                "Spini Road", "Zarghoon Road"
            ],
            "Rawalpindi": [
                "Adiala Road", "Afshan Colony", "Airport Housing Society", "Allama Iqbal Colony", "Askari 14",
                "Bahria Town Rawalpindi", "Banni", "Chaklala Cantt", "Chaklala Scheme 3", "Chakri Road",
                "Chandni Chowk", "Committee Chowk", "Dhamial", "Dhoke Hassu", "Dhoke Kala Khan", "Dhoke Ratta",
                "Gawalmandi", "Gulistan Colony", "Gulraiz", "Harley Street", "Jhanda Chichi", "Khayaban-e-Sir Syed",
                "Kurri Road", "Lalazar", "Lalkurti", "Liaquat Bagh", "Misrial Road", "Morgah", "Murree Road",
                "Muslim Town", "Peshawar Road", "Pirwadhai", "Raja Bazaar", "Range Road", "Saddar", "Sadiqabad",
                "Satellite Town", "Shamsabad", "Tench Bhatta", "Tulsa Road", "Westridge"
            ],
            "Sargodha": [
                "Cantt", "Faisalabad Road", "Iqbal Colony", "Jhal Chakian", "Johar Colony", "Kachehri Bazaar",
                "Lahore Road", "Model Town", "Muslim Town", "Satellite Town", "Sillanwali Road", "University Road"
            ],
            "Sialkot": [
                "Allama Iqbal Town", "Cantt", "Citi Housing Sialkot", "Daska Road", "Defence Road", "DHA Sialkot",
                "Gohadpur", "Hajipura", "Kashmir Road", "Khadim Ali Road", "Model Town", "Paris Road",
                "Pasrur Road", "Rangpura", "Sambrial Road", "Shahabpura", "Small Industrial Estate", "Ugoki",
                "Wazirabad Road"
            ],
            "Sukkur": [
                "Airport Road", "Barrage Road", "Bunder Road", "Military Road", "Minara Road", "New Pind",
                "Old Sukkur", "Queens Road", "Shikarpur Road", "Sukkur Cantt"
            ]
        };

        function $id(id) { return document.getElementById(id); }

        function sortAZ(list) {
            return list.slice().sort(function(a, b) {
                return a.localeCompare(b, 'en', { sensitivity: 'base', numeric: true });
            });
        }

        function notifyChange() {
            var evt;
            try {
                evt = new CustomEvent('checkout:address-changed');
            } catch (e) {
                evt = document.createEvent('Event');
                evt.initEvent('checkout:address-changed', true, true);
            }
            document.dispatchEvent(evt);
        }

        function parts(kind) {
            return {
                hidden: $id('checkout-' + kind),
                select: $id('checkout-' + kind + '-select'),
                custom: $id('checkout-' + kind + '-custom'),
                back: $id('checkout-' + kind + '-back-link')
            };
        }

        // Fill a <select> with placeholder + A-Z items + coloured manual option. Returns true if selectedValue matched.
        function fillSelect(select, items, placeholderText, manualLabel, selectedValue) {
            select.innerHTML = '';
            var placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.disabled = true;
            placeholder.textContent = placeholderText;
            select.appendChild(placeholder);

            var matched = false;
            for (var i = 0; i < items.length; i++) {
                var opt = document.createElement('option');
                opt.value = items[i];
                opt.textContent = items[i];
                if (selectedValue && selectedValue.toLowerCase() === items[i].toLowerCase()) {
                    opt.selected = true;
                    matched = true;
                }
                select.appendChild(opt);
            }

            var manual = document.createElement('option');
            manual.value = MANUAL;
            manual.textContent = manualLabel;
            manual.className = 'checkout-manual-option';
            manual.style.color = '#2563eb';
            manual.style.fontWeight = '700';
            manual.style.backgroundColor = '#eff6ff';
            select.appendChild(manual);

            if (!matched) placeholder.selected = true;
            return matched;
        }

        function showSelectMode(p) {
            p.select.style.display = '';
            p.custom.style.display = 'none';
            p.custom.value = '';
            if (p.back) p.back.style.display = 'none';
        }

        function showManualMode(p, value, allowBack, focus) {
            p.select.style.display = 'none';
            p.custom.style.display = '';
            p.custom.value = value || '';
            p.hidden.value = (value || '').trim();
            if (p.back) p.back.style.display = allowBack ? '' : 'none';
            if (focus) {
                setTimeout(function() { p.custom.focus(); }, 0);
            }
        }

        function isManual(p) {
            return p.custom.style.display !== 'none';
        }

        function populateAreas(preserve) {
            var city = parts('city');
            var area = parts('area');
            if (!area.hidden || !area.select || !area.custom) return;

            var saved = preserve ? (area.hidden.value || '') : '';
            var cityValue = city.hidden ? city.hidden.value.trim() : '';
            var cityIsManual = city.custom && isManual(city);

            if (!cityValue && !cityIsManual) {
                showSelectMode(area);
                area.select.innerHTML = '<option value="" disabled selected>Select City First</option>';
                area.select.disabled = true;
                area.hidden.value = '';
                return;
            }

            var list = (!cityIsManual && pakistanCityAreas[cityValue]) ? sortAZ(pakistanCityAreas[cityValue]) : null;

            if (!list) {
                // No predefined areas for this city -> customer types the area directly.
                area.select.disabled = false;
                showManualMode(area, saved, false, false);
                return;
            }

            area.select.disabled = false;
            var matched = fillSelect(area.select, list, 'Select Area', AREA_MANUAL_LABEL, saved);
            if (saved && !matched) {
                showManualMode(area, saved, true, false);
            } else {
                showSelectMode(area);
                area.hidden.value = matched ? area.select.value : '';
            }
        }

        function populateCities(preserve) {
            var provinceEl = $id('checkout-province');
            var city = parts('city');
            if (!provinceEl || !city.hidden || !city.select || !city.custom) return;

            var province = provinceEl.value;
            var saved = preserve ? (city.hidden.value || city.select.getAttribute('data-selected') || '') : '';

            if (!province || !pakistanProvinceCities[province]) {
                showSelectMode(city);
                city.select.innerHTML = '<option value="" disabled selected>Select Province First</option>';
                city.select.disabled = true;
                city.hidden.value = '';
                populateAreas(false);
                return;
            }

            city.select.disabled = false;
            var matched = fillSelect(city.select, sortAZ(pakistanProvinceCities[province]), 'Select City', CITY_MANUAL_LABEL, saved);
            if (saved && !matched) {
                showManualMode(city, saved, true, false);
            } else {
                showSelectMode(city);
                city.hidden.value = matched ? city.select.value : '';
            }
            populateAreas(preserve);
        }

        var initialized = false;
        function init() {
            if (initialized) return;
            if (!$id('checkout-province') || !$id('checkout-city-select')) return;
            initialized = true;

            var city = parts('city');
            var area = parts('area');

            populateCities(true);
            notifyChange();

            $id('checkout-province').addEventListener('change', function() {
                populateCities(false);
                notifyChange();
            });

            city.select.addEventListener('change', function() {
                if (city.select.value === MANUAL) {
                    showManualMode(city, '', true, true);
                } else {
                    city.hidden.value = city.select.value;
                }
                populateAreas(false);
                notifyChange();
            });

            city.custom.addEventListener('input', function() {
                city.hidden.value = city.custom.value.trim();
                notifyChange();
            });

            if (city.back) {
                city.back.addEventListener('click', function(e) {
                    e.preventDefault();
                    showSelectMode(city);
                    city.select.selectedIndex = 0;
                    city.hidden.value = '';
                    populateAreas(false);
                    notifyChange();
                });
            }

            area.select.addEventListener('change', function() {
                if (area.select.value === MANUAL) {
                    showManualMode(area, '', true, true);
                } else {
                    area.hidden.value = area.select.value;
                }
                notifyChange();
            });

            area.custom.addEventListener('input', function() {
                area.hidden.value = area.custom.value.trim();
                notifyChange();
            });

            if (area.back) {
                area.back.addEventListener('click', function(e) {
                    e.preventDefault();
                    showSelectMode(area);
                    area.select.selectedIndex = 0;
                    area.hidden.value = '';
                    notifyChange();
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
        window.addEventListener('load', init);
    })();
</script>
