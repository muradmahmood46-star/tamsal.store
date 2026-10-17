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
        color: #2563eb !important;
        font-weight: 700 !important;
        background-color: #eff6ff !important;
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

        var pakistanSpecificCityAreas = {
    "Karachi": [
        "Abdullah Haroon Road", "Abul Hassan Isphahani Road", "Ahsanabad", "Airport Area", "Akhtar Colony",
        "Al-Falah Society", "Al-Hilal Society", "Al-Noor Society", "Amil Colony", "Ancholi",
        "Aram Bagh", "Askari 1", "Askari 2", "Askari 3", "Askari 4", "Askari 5", "Azam Basti",
        "Azizabad", "Bahadurabad", "Bahria Town Karachi", "Baldia Town", "Baraf Khana", "Barkat-e-Hyderi",
        "Bath Island", "Bin Qasim Town", "Boat Basin", "Buffer Zone (Sector 15-A, 15-B)", "Burns Road",
        "Cantt (Karachi Cantonment)", "Cantt Station", "Civil Lines", "Clifton (Block 1 to 9)", "Cochinwala Market",
        "Cosmopolitan Society", "Dastagir Colony", "Defance View (Phase 1, 2)", "Defence Garden", "Delhi Colony",
        "DHA City Karachi", "DHA Phase 1", "DHA Phase 2", "DHA Phase 4", "DHA Phase 5 (Ext)", "DHA Phase 6",
        "DHA Phase 7 (Ext)", "DHA Phase 8", "Dhoraji Colony", "Diamond City", "Drigh Colony", "Drigh Road",
        "Falcon Complex", "Faran Society", "Fatima Jinnah Colony", "Federal B Area (Block 1 to 22)", "Firdous Colony",
        "Gadap Town", "Garden East", "Garden West", "Gharibabad", "Gizri", "Golimar", "Golra Sharif",
        "Gulberg Town", "Gulistan-e-Jauhar (Block 1 to 20)", "Gulshan-e-Ameen", "Gulshan-e-Faisal",
        "Gulshan-e-Hadeed (Phase 1, 2)", "Gulshan-e-Iqbal (Block 1 to 19)", "Gulshan-e-Jamal", "Gulshan-e-Kaneez Fatima",
        "Gulshan-e-Malir", "Gulshan-e-Maymar (Sector Q, R, S, T, U, V, W, X, Y, Z)", "Gulshan-e-Osman", "Gulshan-e-Rafi",
        "Gulshan-e-Roomi", "Gulshan-e-Shamim", "Gulshan-e-Umair", "Gulzar-e-Hijri (Scheme 33)", "Guru Mandir",
        "Haidery (North Nazimabad Block G, H)", "Hawkesbay", "Hill Park Area", "Hub River Road", "Hussainabad",
        "I.I. Chundrigar Road", "Jaffar-e-Tayyar Society", "Jamshed Quarters", "Jamshed Road", "Jinnah Avenue",
        "Jinnah International Airport", "Jodia Bazaar", "Jubilee Market Area", "Kalakot", "Karachi University Campus",
        "Karimabad", "Karsaz", "Kashmir Road", "KDA Scheme 1", "KDA Scheme 33", "Kemari", "Khadda Market",
        "Kharadar", "Khudadad Colony", "Khuldabad", "Korangi Creek", "Korangi Crossing", "Korangi Industrial Area",
        "Korangi Sector 1 to 51", "Landhi Industrial Area", "Landhi Sector 1 to 36", "Liaquatabad (Lalukhet 1 to 10)",
        "Lines Area", "Lyari Town", "M.A. Jinnah Road", "Macchar Colony", "Mahmoodabad", "Malir Cantt",
        "Malir City", "Malir Colony", "Malir Halt", "Malir Model Town", "Manghopir", "Manora", "Manzoor Colony",
        "Maripur", "Martin Quarters", "Mehran Town", "Meethadar", "Metroville (SITE)", "Model Colony",
        "Mohammad Ali Memorial Society (MACHS)", "Mominabad", "Mosamiyat", "Muslimabad", "Nagan Chowrangi",
        "Nanak Wara", "Naval Anchorage Karachi", "Naval Colony (Haroon Bahria)", "Nazimabad (Block 1 to 5)",
        "New Chali", "New Karachi (Sector 5-A to 11-L)", "New Rizvia Society", "NIPA Chowrangi Area",
        "North Karachi (Sector 7 to 11)", "North Nazimabad (Block A to W)", "Nursery (PECHS Block 6)",
        "Old Clifton", "Old Golimar", "Orangi Town (Sector 1 to 16)", "PAF Base Faisal Area", "PAF Base Korangi",
        "PAF Base Masroor", "Pakistan Chowk", "Pankha Hotel Area", "Patel Para", "PECHS (Block 2, 3, 6)",
        "PIDC House Area", "PIB Colony", "Pir Illahi Buksh Colony", "Port Qasim Area", "Punjab Colony",
        "Qasba Colony", "Qayyumabad", "Quaidabad", "Ranchore Line", "Rashid Minhas Road", "Regal Chowk Area",
        "Rizvia Society", "Saadi Town", "Saddar Bazaar", "Saeedabad", "Safoora Goth", "Sakhi Hassan",
        "Scheme 33 (Sector 17-A to 52)", "Sea View", "Secunderabad", "Shadman Town", "Shah Faisal Colony (Block 1 to 5)",
        "Shaheed-e-Millat Road", "Shahrah-e-Faisal Area", "Shahrah-e-Liaquat", "Shahrah-e-Pakistan",
        "Shanti Nagar", "Sharfabad", "Sher Shah Colony", "Sindhi Muslim Housing Society (SMCHS)", "SITE Area",
        "Soldier Bazaar (1, 2, 3)", "Sohrab Goth", "State Life Society", "Steel Town", "Surjani Town (Sector 4 to 10)",
        "Tariq Road", "Teen Hatti", "Tipu Sultan Road", "Tower (Merewether Tower)", "University Road",
        "Urdu Bazaar", "Usmanabad", "Water Pump (Federal B Area Block 16)", "West Wharf", "Yaseenabad", "Zamzama"
    ],
    "Lahore": [
        "Abid Market", "Abbot Road", "Airline Housing Society", "Al-Faisal Town", "Al-Hamra Town",
        "Al-Jalil Garden", "Al-Madina Colony", "Al-Noor Town", "Al-Rehman Garden (Phase 1 to 7)", "Ali Town",
        "Ali View Garden", "Allama Iqbal Medical Colony", "Allama Iqbal Town (Asif, Chenab, Huma, Khyber, Mehran, Neelam, Nizam, Pak, Ravi, Raza, Sikandar, Umar, Zeenat Block)",
        "Amer Sidhu", "Amin Park", "Anarkali Bazaar", "Androon Shehr (Walled City)", "Architects Housing Society",
        "Arya Nagar", "Asif Town", "Askari 1", "Askari 2", "Askari 3", "Askari 4", "Askari 5", "Askari 9",
        "Askari 10", "Askari 11", "Awan Town", "Azam Cloth Market", "Aziz Bhatti Town", "Badami Bagh",
        "Baghbanpura", "Bahar Colony", "Bahria Education & Medical City", "Bahria Nasheman",
        "Bahria Orchard (Phase 1, 2, 3, 4)", "Bahria Town (Sector A, B, C, D, E, F)", "Barki Road", "Barkat Market",
        "Bedian Road", "Begum Kot", "Beadon Road", "Bhati Gate", "Bhoptian Chowk", "Bilal Gunj", "BOR Society",
        "Bostan Colony", "Brandreth Road", "Bund Road", "Canal Bank Scheme", "Canal Berg", "Canal Garden",
        "Canal View Housing Scheme", "Cantt (Lahore Cantonment)", "Cavalry Ground", "Cavalry Extension",
        "Central Park Housing Scheme", "Chauburji", "Chauburji Park", "Chinar Bagh", "Chowk Yateem Khana",
        "Chungi Amar Sidhu", "Circular Road", "Civil Defence Colony", "Civil Lines", "CMH Area", "Darogha Wala",
        "Data Darbar Area", "Davis Road", "Defence Road", "Delhi Gate", "Dev Samaj Road", "Dharampura",
        "DHA 11 Rahbar (Phase 1, 2)", "DHA EME Sector", "DHA Phase 1", "DHA Phase 2", "DHA Phase 3",
        "DHA Phase 4", "DHA Phase 5", "DHA Phase 6", "DHA Phase 7", "DHA Phase 8 (Ex Air Avenue / Park View)",
        "DHA Phase 9 Prism", "DHA Phase 9 Town", "Doctors Society", "Eden Avenue", "Eden Boulevard",
        "Eden City", "Eden Gardens", "Eden Park", "Eden Value Homes", "Egerton Road", "Empress Road",
        "Engineers Town (Phase 1, 2)", "Etihad Town (Phase 1, 2)", "Faisal Town (Block A, B, C)", "Falcon Complex",
        "Fateh Garh", "Fazaia Housing Scheme (Phase 1, 2)", "Ferozepur Road", "Fortress Stadium Area",
        "Gajjumatta", "Garden Town (Babur, Tariq, Usman, Aibak, Ali, Abu Bakar Block)", "Garhi Shahu", "Gawalmandi",
        "Ghaziabad", "Ghazi Road", "Ghosia Colony", "Gor-1", "Gor-2", "Gor-3", "Green Cap Housing Society",
        "Green Park", "Green Town (Sector 1, 2, 3, 4, 5)", "GT Road (Lahore bypass)", "Gujjar Pura",
        "Gulbahar Colony", "Gulberg 1", "Gulberg 2", "Gulberg 3", "Gulberg 4", "Gulberg 5", "Guldasht Town",
        "Gulshan-e-Lahore", "Gulshan-e-Mustafa", "Gulshan-e-Ravi (Block A to G)", "Gulshan-e-Shalimar", "Guru Mangat",
        "Hall Road", "Harbanspura", "Ichhra", "Ideal Garden Housing Scheme", "Iqbal Park", "Islam Park",
        "Islampura (Krishan Nagar)", "Islamia Park", "Izmir Town", "Jaffar Town", "Jail Road", "Jallo",
        "Jallo Park Area", "Jamil Town", "Jinnah Hospital Area", "Johar Town (Phase 1 Block A to H)",
        "Johar Town (Phase 2 Block J to R)", "Jubilee Town", "Kacha Jail Road", "Kahna Nau", "Kala Khatai Road",
        "Kallar Syedan Road", "Kalma Chowk Area", "Kashmir Road", "Khyaban-e-Amin (Block A to R)", "Khyaban-e-Jinnah",
        "Kot Abdul Malik", "Kot Khawaja Saeed", "Kot Lakhpat", "Kotli Peer Abdul Rehman", "Krishan Nagar",
        "Lake City (Sector M1 to M8)", "Lakshmi Chowk", "Lal Pul", "Lalazar Housing Scheme", "Lawrence Road",
        "LDA Avenue 1 (Block A to M)", "LDA City", "Liberty Market Area", "Link Road Model Town", "Loha Market",
        "Lohari Gate", "Lower Mall", "Madina Town", "Main Market Gulberg", "Mall Road (The Mall)",
        "Manga Mandi", "Mansoorah", "Maraca", "Marghazar Colony", "Mateen Avenue", "Mayo Hospital Area",
        "McLeod Road", "Mehar Fayyaz Colony", "Mian Mir Colony", "Mian Plaza", "Mianwali Colony",
        "Military Accounts Society", "Misri Shah", "Model Colony", "Model Town (Block A to M)",
        "Model Town Extension", "Mohni Road", "Mozang", "Mughalpura", "Multan Road", "Muslim Town (Old / New)",
        "Mustafa Town", "Nasheman-e-Iqbal (Phase 1, 2)", "National Town", "Naval Anchorage Lahore",
        "Nawab Town", "Nazir Garden Society", "Nespak Housing Scheme (Phase 1, 2)", "Nisbat Road",
        "Nishtar Colony", "Nizamabad", "Nobel Town", "Nonarian", "NTC Housing Scheme", "Officers Colony Cantt",
        "Old Anarkali", "Pak Arab Housing Society (Phase 1, 2)", "Pakki Thatti", "Pantheist Colony",
        "Paragon City (Imperial, Orchard, Woods, Barki Garden)", "Park Avenue Housing Scheme",
        "Park View City (Tulip, Jasmine, Rose, Topaz, Jade, Pearl Block)", "Passco Housing Society",
        "PCSIR Housing Society (Phase 1, 2)", "Peoples Colony", "Phool Nagar Area", "PIA Housing Society (Phase 1, 2)",
        "PIDC Housing Society", "Pine Avenue", "Poonch Road", "PRC Housing Society", "Prem Nagar",
        "Punjab Coop Housing Society (Block 1 to 5)", "Punjab Govt Employees Housing Scheme",
        "Punjab Small Industries Housing Scheme", "Punjab University Employees Housing Scheme (Town Campus)",
        "Punjab University New Campus", "Qaddafi Stadium Area", "Qaid-e-Azam Industrial Estate",
        "Qartaba Chowk Area", "Qasim Pura", "Qila Gujar Singh", "Qila Lachman Singh", "Rail Town",
        "Railway Colony (Carriage Shop)", "Railway Headquarters Area", "Railway Station Area", "Raiwind City",
        "Raiwind Road", "Rana Town", "Rang Mahal", "Ravi Clifton", "Ravi Road", "Rehman Gardens",
        "Rehmanpura", "Resham Gali", "Revenue Employees Cooperative Housing Society (RECHS)", "Riwaz Garden",
        "Royal Residencia", "Sabzazar (Block A to P)", "Saddar Bazaar Cantt", "Saeed Park", "Sahowala",
        "Salamatpura", "Samanabad", "Sanda (Khurd, Kalan)", "Sant Nagar", "Sarba", "Sarwar Town",
        "Satellite Town Lahore", "Scarsdale National Colony", "Sehjpal", "Shabbir Town", "Shadab Colony",
        "Shadbagh", "Shadman 1", "Shadman 2", "Shah Di Khoi", "Shah Faisal Town", "Shah Jamal",
        "Shahdara Town", "Shahkam Chowk", "Shahpur Kanjran", "Shalimar Link Road", "Shalimar Town",
        "Shama Chowk Area", "Sham Nagar", "Shaukat Khanum Hospital Area", "Shera Kot", "Sher Shah Colony",
        "Singhpura", "State Life Cooperative Housing Society (Phase 1, 2)", "Sui Gas Housing Society (Phase 1, 2)",
        "Sukh Chayn Gardens", "Sunfort Gardens", "Super Town", "Taj Bagh Scheme", "Tajpura Scheme",
        "Tariq Garden", "Tech Society", "Tezab Ahata", "Thokar Niaz Baig", "Timber Market",
        "Tipu Block Garden Town", "Township (Sector A-1, A-2, B-1, B-2, C-1, C-2, D-1, D-2)",
        "Tricon Village", "Trust Colony", "Ulfat Town", "Upper Mall", "Urdu Bazaar Lahore",
        "Valencia Town (Block A to P)", "Vehari Colony", "Wahdat Colony", "Wahdat Road",
        "Walled City (Delhi, Lohari, Bhati, Kashmiri, Sheranwala Gate)", "Walton Road (Cantt)",
        "Wapda Town (Phase 1 Block A to K)", "Wapda Town (Phase 2 Block M to Q)", "Waris Road",
        "West Wood Colony", "Yateem Khana", "Yousaf Colony", "Zafar Colony", "Zaman Park", "Zero Point Lahore"
    ],
    "Islamabad": [
        "Airport Enclave", "B-17 Multi Gardens (Block A, B, C, D, E, F, G)", "Bahria Enclave (Sector A to P)",
        "Bahria Golf City", "Bahria Town Islamabad (Phase 2 to 9)", "Bani Gala", "Bhara Kahu",
        "Blue Area", "C-14", "C-15", "C-16", "Chak Shahzad", "Chattah Bakhtawar", "D-12 (Sector 1, 2, 3, 4)",
        "D-13", "D-14", "D-17 (Cabinet Division / MPCHS)", "D-18", "DHA Islamabad Phase 1 (Orchard)",
        "DHA Islamabad Phase 2 (Sector A to V)", "DHA Islamabad Phase 3", "DHA Islamabad Phase 4",
        "DHA Islamabad Phase 5", "DHA Valley", "Diplomatic Enclave", "E-7", "E-8 (Naval Complex)",
        "E-9 (Air Force Complex)", "E-10", "E-11 (Multi Gardens / FECHS / Sector 1 to 4)", "E-12",
        "E-16 (Cabinet Division)", "E-17 (Cabinet Division)", "F-5", "F-6 (Super Market, Sector 1 to 4)",
        "F-7 (Jinnah Super, Sector 1 to 4)", "F-8 (Ayub Market, Sector 1 to 4)", "F-9 (Fatima Jinnah Park Area)",
        "F-10 (Markaz, Sector 1 to 4)", "F-11 (Markaz, Sector 1 to 4)", "F-12", "F-15 (Khayaban-e-Kashmir)",
        "F-17 (Tele Gardens)", "Farash Town", "G-5", "G-6 (Melody Market / Aabpara)", "G-7 (Sitara Market)",
        "G-8 (Markaz / Sector 1 to 4)", "G-9 (Karachi Company, Markaz, Sector 1 to 4)", "G-10 (Markaz, Sector 1 to 4)",
        "G-11 (Markaz, Sector 1 to 4)", "G-12", "G-13 (Markaz, Sector 1 to 4)", "G-14 (Sector 1, 2, 3, 4)",
        "G-15 (Jammu & Kashmir Society)", "G-16", "G-17", "Ghauri Town (Phase 1 to 8)", "Golra Sharif",
        "Gulberg Greens (Executive, Farmhouses)", "Gulberg Residencia (Block A to V)", "H-8 (Educational Sector)",
        "H-9", "H-10 (IIUI Campus Area)", "H-11", "H-12 (NUST Campus Area)", "H-13", "Humak Model Town",
        "I-8 (Markaz, Sector 1 to 4)", "I-9 (Industrial Area, Sector 1 to 4)", "I-10 (Markaz, Sector 1 to 4)",
        "I-11 (Fruit Market Area)", "I-12", "I-14 (Riphah Campus Area)", "I-15", "I-16", "I-17",
        "Islamabad Expressway Area", "Jinnah Garden (Phase 1, 2)", "Kashmir Highway Area", "Korang Town",
        "Koral Chowk Area", "Kuri Road", "Lehtrar Road", "Loi Bher", "Margalla Town (Phase 1, 2)",
        "Media Town", "Model Town Humak", "Motorway City", "National Police Foundation (O-9)",
        "Naval Anchorage (Sector A to J)", "New Islamabad Airport Area", "Nilore", "Pakistan Town (Phase 1, 2)",
        "Park Enclave (Phase 1, 2, 3)", "Park Road", "Park View City (Block A to J)", "PWD Housing Society",
        "Rawal Dam Area", "Rawal Town", "Rawat Industrial Area", "River Garden Housing Scheme",
        "Sangjani", "Sarai Kharbuza", "Shah Allah Ditta", "Shahzad Town", "Sihala", "Soan Garden (Block A to H)",
        "Spring Valley", "Swan Garden", "Tarlai Kalan", "Tarnol", "Top City-1", "University Town Islamabad",
        "Zaraj Housing Society (Sector A, B, C)", "Zero Point Islamabad", "Zone 4", "Zone 5"
    ],
    "Rawalpindi": [
        "Adiala Road", "Afshan Colony", "Airport Road", "Airport Housing Society", "Ali Town", "Allama Iqbal Colony",
        "Al-Noor Colony", "Amir Town", "Arya Mohalla", "Asghar Mall Scheme", "Askari 1", "Askari 2",
        "Askari 3", "Askari 5", "Askari 7", "Askari 10", "Askari 11", "Askari 12", "Askari 13", "Askari 14",
        "Ayub National Park Area", "Babu Lal Hussain Road", "Bahria Town Phase 1 to 6", "Bahria Town Phase 7 to 8",
        "Bakramandi", "Banni Chowk", "Baraf Khana", "Carriage Factory Area", "Chaklala Cantt", "Chaklala Scheme 1",
        "Chaklala Scheme 2", "Chaklala Scheme 3", "Chakri Road", "Chamanzar Colony", "Chandni Chowk",
        "Chaur Harpal", "Circular Road", "Civil Lines", "Commercial Market (Satellite Town)", "Committee Chowk",
        "DHA Phase 1 (Rawalpindi)", "DHA Phase 2 (Rawalpindi)", "Dhamial Camp", "Dhamial Road", "Dhoke Ali Akbar",
        "Dhoke Babu Irfan", "Dhoke Banaras", "Dhoke Chiraghdin", "Dhoke Dalal", "Dhoke Farman Ali", "Dhoke Gangal",
        "Dhoke Gujran", "Dhoke Hassu", "Dhoke Hameeda", "Dhoke Kala Khan", "Dhoke Kashmirian", "Dhoke Khabba",
        "Dhoke Mangtal", "Dhoke Matkal", "Dhoke Munshi", "Dhoke Mustaqeem", "Dhoke Naju", "Dhoke Paracha",
        "Dhoke Ratta", "Dhoke Sayedan", "Eidgah Road", "Faizabad Chowk Area", "Fazaia Colony", "Gawalmandi",
        "Girja Road", "Gohar Save Scheme", "Gulbahar Scheme", "Gulistan Colony", "Gulraiz Housing Scheme (Phase 1 to 8)",
        "Gulshan Dadan Khan", "Gulshan-e-Abad", "Gulshan-e-Khudadad", "Gulshan-e-Saeed", "Gulzar-e-Quaid",
        "Harley Street", "High Court Road", "Husseinabad", "Imambargah Road", "Jalal Din Town", "Jamarghata",
        "Jhanda Chichi", "Jhelum Road", "Judicial Town", "Kahuta Road", "Kalma Chowk", "Kartarpura", "Kashmiri Mohalla",
        "Kashmir Road", "Kattarian", "Khanna Dak", "Khanna Kak", "Khanna Pul", "Khayaban-e-Sir Syed (Sector 1 to 4)",
        "Khurram Colony", "Kohati Bazaar", "KRL Road", "Kuri Road", "Lalazar (New / Old)", "Lalkurti",
        "Liaquat Bagh Area", "Liaquat Colony", "Madina Colony", "Makkah Town", "Mall Road Cantt", "Marir Hassan",
        "Media Town Rawalpindi", "Millat Colony", "Misrial Road", "Mohan Pura", "Morgah", "Moti Bazaar",
        "Murree Road", "Muslim Town", "Nawababad", "Naya Nazimabad Rawalpindi", "New Afzal Town", "New Lalazar",
        "Nishtar Colony", "Officers Colony Cantt", "Old Airport Area", "Peshawar Road", "Pirwadhai",
        "Police Foundation", "Potohar Town", "PWD Road", "Qasim Market Cantt", "Quaid-e-Azam Colony",
        "Railway Colony Cantt", "Railway Scheme 7", "Raja Bazaar", "Range Road", "Rawal Road", "Rawat",
        "Rehmanabad", "Saddar Cantt (Bank Road, Haider Road, Adamjee Road)", "Sadiqabad", "Safari Villas (1, 2, 3)",
        "Saidpur Road", "Satellite Town (Block A, B, C, D, E, F)", "Scheme 3 Cantt", "Shabbir Town",
        "Shah Khalid Colony", "Shalley Valley", "Shamsabad", "Sher Zaman Colony", "Sikandarabad", "Sixth Road",
        "T&T Colony", "Tariqabad", "Tench Bhatta", "Tulsa Road", "Urdu Bazaar Rawalpindi", "Wahdat Colony",
        "Waris Khan", "Westridge 1", "Westridge 2", "Westridge 3", "Zaffar-ul-Haq Road", "Zia-ul-Haq Colony"
    ],
    "Faisalabad": [
        "Abdullah Garden", "Abdullahpur", "Al-Faisal Garden", "Al-Najaf Colony", "Ali Housing", "Allama Iqbal Colony",
        "Amin Town", "Anand Pura", "Ayub Agricultural Research Area", "Batala Colony", "Bawa Chak", "Bhaiwala",
        "Canal Gardens", "Canal Park", "Canal Road", "Chak 208 RB", "Chak Jhumra Road", "Chenab Club Area",
        "Chiniot Bazaar", "Citi Housing Faisalabad (Phase 1, 2)", "Civil Lines", "Clock Tower (Ghanta Ghar 8 Bazaars)",
        "D Ground (Peoples Colony No. 1)", "Daewoo Road", "Dijkot Road", "Dhudiwala", "Dogar Basti", "Eden Garden",
        "Eden Valley", "Faisal Garden", "Faisal Town", "Faizabad", "FDA City", "Gatwala", "Ghulam Muhammad Abad",
        "Gobind Pura", "Green Town", "Gulbahar Colony", "Gulberg (A, B, C)", "Gulistan Colony (1, 2)", "Gulshan Colony",
        "Guru Nanak Pura", "Haji Abad", "Harcharan Pura", "Hasan Town", "Ideal Colony", "Industrial Estate",
        "Iqbal Stadium Area", "Islam Nagar", "Jail Road", "Jamilabad", "Jaranwala Road", "Jhang Bazaar",
        "Jhang Road", "Jinnah Colony", "Kamal Town", "Karkhana Bazaar", "Kashmir Road", "Katchery Bazaar",
        "Khayaban Colony (1, 2, 3)", "Kohinoor City", "Kohinoor Flats", "Kotwali Road", "Lasani Garden",
        "Lasani Town", "Liaquat Town", "Lorry Adda Area", "Lyallpur Galleria Area", "Madina Town", "Mai Di Jhugi",
        "Mall Road", "Manawala", "Mansoorabad", "Marzi Pura", "Millat Road", "Millat Town", "Model City (1, 2)",
        "Model Town", "Montgomery Bazaar", "Motorway City", "Muhammad Nagar", "Mujahid Colony", "Muslim Town",
        "Muzaffar Colony", "Nagina Colony", "Naithan Pura", "Nisar Colony", "Nishatabad", "Novelty Chowk Area",
        "Officers Colony (1, 2)", "Peoples Colony No. 1", "Peoples Colony No. 2", "Railway Road", "Rail Bazaar",
        "Raza Abad", "Razabad", "Regency Plaza Area", "Rex City Area", "Saeed Colony (1, 2)", "Saeedabad",
        "Saleemi Chowk Area", "Samanabad", "Samundri Road", "Sargodha Road", "Satiana Road", "Shadab Colony",
        "Shahzad Colony", "Sheikhupura Road", "Sitara Colony", "Sitara Sapna City", "Susan Road", "Tariqabad",
        "Tech Town", "Timber Market", "University of Agriculture Campus", "Wapda City", "Warispura", "Yousaf Abad"
    ],
    "Multan": [
        "Abdali Road", "Al-Quraish Housing Scheme", "Ali Town", "Altaf Town", "Ansari Colony", "Askari Colony (1, 2)",
        "Bagh Lange Khan", "Bahadurpur", "Bawa Safra", "Bomanji Square", "Bosan Road", "Buch Executive Villas",
        "Cantt (Multan Cantonment)", "Chah Boharwala", "Chowk Kumharanwala", "Chowk Shaheedan", "Chungi No. 1",
        "Chungi No. 6", "Chungi No. 7", "Chungi No. 8", "Chungi No. 9", "Chungi No. 14", "Circular Road",
        "Citi Housing Multan", "Civil Lines", "Daulat Gate", "Defence Officer Colony", "Delhi Gate", "DHA Multan (Sector A to Y)",
        "Dera Adda", "Double Phatak", "Faiz Town", "Fatima Jinnah Town", "Garden Town", "General Bus Stand Area",
        "Ghanta Ghar Area", "Ghaziabad", "Gulberg Colony", "Gulgasht Colony", "Gulshan Market", "Gulshan-e-Mehar",
        "Haram Gate", "Harmain City", "Hassan Parwana Colony", "Hussain Agahi Bazaar", "Industrial Estate",
        "Ismail Abad", "Jalilabad", "Jamilabad", "Jinnah Town", "Kalma Chowk", "Katchery Road", "Khanewal Road",
        "Kotla Tolay Khan", "Lohari Gate", "Lutfabad", "Madina Colony", "Mall Road Cantt", "Masoom Shah Road",
        "Mattital Road", "MDA Chowk", "Model Town (A, B, C)", "Mumtazabad", "Nagas Shah", "Naval Base Area",
        "New Multan (Block A to Z)", "New Shalimar Colony", "Nishtar Road", "Northern Bypass", "Officers Colony",
        "Old Shujabad Road", "Pak Gate", "Peer Khurshid Colony", "Peoples Colony", "Qaddafi Chowk Area",
        "Qasim Bela", "Qasim Pur Colony", "Railway Colony", "Raza Abad", "Royal Orchard", "Sameeja Abad",
        "Shah Gardez", "Shah Rukn-e-Alam Colony (Phase 1, 2)", "Shah Shams Colony", "Shalimar Colony",
        "Shamasabad Colony", "Shershah Road", "Sooraj Miani", "Suraj Kund Road", "Tariq Road", "Timber Market",
        "Vehari Chowk", "Vehari Road", "Wapda Town (Phase 1, 2, 3)", "Zakariya Town"
    ],
    "Peshawar": [
        "Abdara Road", "Academy Town", "Ahmadabad", "Airport Road", "Al-Haram Town", "Ali Town", "Arbab Road",
        "Ashrafia Colony", "Askari 1", "Askari 2", "Askari 3", "Askari 4", "Askari 5", "Askari 6", "Bacha Khan Chowk",
        "Badhaber", "Bahadar Kalay", "Bara Road", "Bhanamari", "Board Bazaar", "Cantt (Peshawar Cantonment)",
        "Chamkani", "Charsadda Road", "Chowk Yadgar", "Civil Quarters", "Dabgari Gardens", "Dalazak Road",
        "Danishabad", "Deans Trade Centre Area", "Defence Colony", "DHA Peshawar (Sector A to J)", "Doranpur",
        "Faqirabad (1, 2)", "Forest Bazar", "G.T. Road Peshawar", "Garhi Qamar Din", "Gora Qabristan Area",
        "Gulbahar (No. 1, 2, 3, 4)", "Gulberg", "Gulshan-e-Rahman", "Gulshan-e-Umar", "Haji Camp Area",
        "Hashtnagri", "Hayatabad (Phase 1 to 7)", "Industrial Estate (Hayatabad)", "Islamia College Area",
        "Jehangir Pura", "Karkhano Market", "Karimpura", "Khyber Bazaar", "Kohat Road", "Kotla Mohsin Khan",
        "Lahori Gate", "Landi Arbab", "Malik Pura", "Mall Road Cantt", "Mathra", "Michni Road", "Momin Town",
        "Nasir Bagh Road", "Nauthia (Jadid, Qadeem)", "New Rampura", "Nishtarabad", "Old City (Androon Shehr)",
        "Pajagi Road", "Palosi", "Palosi Road", "Peshawar University Campus", "Phandu Road", "Pishtakhara",
        "Qissa Khwani Bazaar", "Rahatabad", "Railway Road", "Ramdas", "Regi Model Town (Zone 1 to 5)",
        "Ring Road (Northern / Southern)", "Saddar Road Cantt", "Saeedabad", "Shaheen Town", "Shahi Bagh Area",
        "Shahpur", "Shami Road Cantt", "Sheikh Muhammadi", "Sikandar Town", "Sufaid Dheri", "Sunehri Masjid Road",
        "Tahkal Bala", "Tahkal Payan", "Tehkal", "Tilakpura", "University Road", "University Town", "Wapda Town",
        "Warsak Road", "Wazir Bagh Area", "Yakatoot", "Yousafabad", "Zaryab Colony"
    ],
    "Quetta": [
        "Abdul Sattar Road", "Ahmad Khanzai", "Airport Road", "Alamdar Road", "Al-Gilani Housing",
        "Arbab Karam Khan Road", "Askari Checkpost Area", "Askari Park Area", "Baleli", "Barah Khana",
        "Brewery Road", "Burma Hotel Area", "Cantt (Quetta Cantonment)", "Chaman Housing Scheme", "Chiltan Housing Scheme",
        "Chashma Achozai", "Circular Road", "Civil Hospital Area", "Civil Lines", "Customs Colony", "Double Road",
        "Eastern Bypass", "Fatima Jinnah Road", "Gawalmandi", "Ghafoor Town", "Ghosia Colony", "Gurdat Singh Road",
        "Hanna Urak", "Harnai Bus Stop Area", "Hazara Town", "Hudda", "Imdad Chowk Area", "Jabal-e-Noor",
        "Jinnah Road", "Jinnah Town", "Joint Road", "Kandahari Bazaar", "Kasi Road", "Killi Almas", "Killi Deba",
        "Killi Gul Muhammad", "Killi Ismail", "Killi Kamalo", "Killi Kotwal", "Killi Paind Khan", "Killi Shabak",
        "Killi Shabo", "Killi Umar", "Kuchlak Road", "Liaquat Bazaar", "Link Road", "Malguzar Road", "Manan Chowk Area",
        "Marriabad", "Masjid Road", "McConaghey Road", "Mission Road", "Model Town", "Murree Abad", "Nawa Killi",
        "New Hanna", "Nodhiez", "Pashtunabad", "Patel Bagh", "Pishin Stop Area", "Prince Road", "Quarry Road",
        "Railway Colony", "Raisani Road", "Sabzal Road", "Sadiq Shaheed Park Area", "Samungli Road", "Samungli Town",
        "Sariab Custom", "Sariab Millat", "Sariab Road", "Satellite Town (Block 1 to 5)", "Shahbaz Town (Phase 1 to 4)",
        "Shaldara", "Shawalkot", "Sirki Road", "Spinny Road", "Stewart Road", "Suraj Gunj Bazaar", "Survey 144",
        "Toghi Road", "Wahdat Colony", "Western Bypass", "Yateem Khana Area", "Zarghoon Road", "Ziarat Stop Area"
    ],
    "Gujranwala": [
        "Al-Rehman Town", "Ali Town", "Allama Iqbal Town", "Aroop", "Baghbanpura", "Bakhtaywala", "Cantt (Gujranwala Cantonment)",
        "Chan Da Qila", "Chenab Gate", "Citi Housing (Phase 1, 2)", "Civil Lines", "Climax Town", "Commissioner Road",
        "DC Colony", "Delta Road", "Dhulley", "Dusehra Ground Area", "Eminabad Road", "Ferozewala Road",
        "Faisal Town", "Garjakh", "Ghosia Colony", "Gondlanwala Road", "Grand Trunk Road (G.T. Road)", "Gulabpura",
        "Gulshan Colony", "Guru Nanak Pura", "Hafizabad Road", "Industrial Estate (1, 2)", "Islam Pura",
        "Jagatpura", "Jinnah Road", "Kacheri Road", "Kamoke Road", "Kangniwala", "Khiali", "Khiali Shahpur",
        "Khurram Colony", "Kotli Rustam", "Ladhewala Waraich", "Liaquat Town", "Lohianwala", "Master City",
        "Model Town", "Mughal Pura", "Mujahid Pura", "Muslim Town", "Nandi Pur", "Nowshera Road", "Palm City",
        "Pasrur Road", "Peoples Colony", "Qila Didar Singh", "Rachna Town", "Rahwali Cantt", "Rasheed Colony",
        "Ratta Bajwa", "Saddar Bazaar", "Satellite Town (Block A to D)", "Shaheen Abad", "Sheikhupura Road",
        "Sialkot Bypass", "Sialkot Road", "Sui Gas Office Area", "Tetley Road", "Trust Colony", "Wapda Town (Phase 1, 2)"
    ],
    "Sialkot": [
        "Abbot Road", "Adda Pasrurian", "Aik Bridge Area", "Allama Iqbal Chowk", "Allama Iqbal Town", "Al-Madina Town",
        "Arazi Yaqub", "Askari Colony", "Bano Bazaar", "Bhalli Town", "Bhoth", "Bonkan", "Cantt (Sialkot Cantonment)",
        "Circular Road", "Citi Housing Sialkot", "Civil Lines", "Commissioner Road", "Daska Road", "Defence Road",
        "DHA Sialkot", "Do Darwaza", "Fatehgarh", "Gohadpur", "Greenwood Street", "G.T. Road Sialkot", "Gulshan Iqbal",
        "Hajipura", "Harrison Road", "Hunter Pura", "Imam Sahib Road", "Islamia College Road", "Jail Road",
        "Kashmir Road", "Khadim Ali Road", "Kotli Behram", "Kotli Loharan", "Kutchery Road", "Magtown",
        "Marakiwal", "Model Town", "Mubarakpura", "Mughalpura", "Muzaffar Pur", "Murray College Road", "Nekapura",
        "Nishat Park Area", "Pakka Garha", "Paris Road", "Pasrur Road", "Poorani Mandi", "Prem Nagar", "Pul Aik",
        "Raja Bazaar", "Rangpura", "Rasool Nagar", "Saddar Bazaar Cantt", "Sambrial Road", "Satellite Town",
        "Shahabpura", "Shatabgarh", "Small Industrial Estate", "Sohawa", "Sublime Chowk Area", "Tajpura",
        "Trunk Bazaar", "Ugoke", "Wazirabad Road", "Zafarwal Road"
    ],
    "Hyderabad": [
        "Al-Mustafa Town", "Al-Rahim Town", "Amil Colony", "Auto Bahn Road", "Badin Stop Area", "Bhittai Nagar",
        "Cantt (Hyderabad Cantonment)", "Citizen Colony", "Civil Lines", "Cloth Market Area", "Daman-e-Kohsar",
        "Defence Colony", "Doctor Colony", "Faqir Ka Pir", "Fateh Chowk", "Gari Khata", "Gidu Chowk Area",
        "Goods Naka", "Government College Area", "Gulistan-e-Sajjad", "Gulshan-e-Hali", "Gulshan-e-Mehran",
        "Gulshan-e-Sehar", "Hala Naka", "Happy Homes", "Heerabad", "Hirabad", "Hussainabad", "Industrial Area",
        "Isra University Area", "Jail Road", "Jamshoro Road", "Kali Mori", "Katchery Road", "Kohsar", "Kotri Barrage Area",
        "Latifabad Unit 1", "Latifabad Unit 2", "Latifabad Unit 3", "Latifabad Unit 4", "Latifabad Unit 5",
        "Latifabad Unit 6", "Latifabad Unit 7", "Latifabad Unit 8", "Latifabad Unit 9", "Latifabad Unit 10",
        "Latifabad Unit 11", "Latifabad Unit 12", "Liaquat Colony", "Makki Shah Road", "Market Tower", "Memon Society",
        "Mirpurkhas Road", "Mubarak Colony", "Naseem Nagar", "New Cloth Market", "Niaz Stadium Area", "Old City",
        "Pakora Stop", "Pareetabad", "Phuleli", "Poona Abad", "Qasimabad Phase 1", "Qasimabad Phase 2",
        "Railway Colony", "Resham Gali", "Rishi Ghat", "Saddar Bazaar", "Sarfaraz Colony", "Shah Latif Colony",
        "Shahi Bazaar", "Sindh University Society", "Site Area", "State Life Colony", "Station Road",
        "Tando Agha", "Tando Jam Road", "Tando Tayyab", "Thandi Sarak", "Tilak Incline", "Wadhu Wah Road"
    ],
    "Bahawalpur": [
        "Abbasia Town", "Ahmedpur Road", "Al-Haram Model Town", "Al-Madina Colony", "Baghdad-ul-Jadeed",
        "Basti Dewan", "Basti Ghafoor", "Bindra Puli", "Cantt (Bahawalpur Cantonment)", "Circular Road",
        "Civil Lines", "Commercial Area", "Darbar Mahal Area", "DHA Bahawalpur (Sector A to N)", "Dubai Mahal Road",
        "Fareed Gate", "General Bus Stand Area", "Hashmi Garden", "Hasilpur Road", "Industrial Estate",
        "Islamic Colony", "Jhangiwala Road", "Jinnah Colony", "Khatm-e-Nabuwwat Chowk", "Model Town A",
        "Model Town B", "Model Town C", "Muhammadia Colony", "Muslim Town", "Noor Mahal Area", "One Unit Staff Colony",
        "Railway Colony", "Saddar Bazaar Cantt", "Satellite Town", "Saraiki Chowk Area", "Shadman City",
        "Shahi Bazaar", "Shikarpuri Gate", "Trust Colony", "University Town", "Wapda Colony", "Yazman Road"
    ],
    "Sargodha": [
        "49 Tail", "47 Pull", "Al-Rehman City", "Allama Iqbal Colony", "Aziz Bhatti Town", "Block 1 to 30",
        "Cantt (Sargodha Cantonment)", "Civil Lines", "Club Road", "DHA Sargodha", "Faisalabad Road",
        "Farooq Colony", "Fatima Jinnah Colony", "G.T. Road Sargodha", "General Bus Stand Area", "Gulshan-e-Bashir",
        "Hameed Town", "Iqbal Colony", "Islam Pura", "Jhal Chakian Road", "Jinnah Colony", "Johar Colony",
        "Kachehri Bazaar", "Khayaban-e-Naveed", "Kot Fareed", "Lahore Road", "Liaquat Market", "Luqman",
        "Madina Colony", "Maryam Town", "Model Town", "Muslim Town", "Niazi Colony", "PAF Base Mushaf Area",
        "Peer Muhammad Colony", "Qartaba Town", "Railway Colony", "Rehman Pura", "Remount Depot Area",
        "Satellite Town (Block A to D)", "Shaheen Park", "Sillanwali Road", "Stadium Road", "Tariqabad",
        "Trust Plaza Area", "University Road", "Wapda Colony", "Zafar Colony"
    ],
    "Sukkur": [
        "Airport Road", "Ayub Gate Area", "Bagh Hayat Ali Shah", "Barrage Colony", "Barrage Road", "Bunder Road",
        "Cantt (Sukkur Cantonment)", "City Bypass", "Clock Tower Area", "Dolphin Chowk Area", "Gharibabad",
        "Gol Market", "Hussainabad", "Lab-e-Mehran", "Loco Shed Colony", "Main Bazaar", "Miani Road",
        "Military Road", "Minara Road", "Mirch Bazaar", "Mission Road", "Modern Colony", "Muslim Colony",
        "New Goth", "New Pind", "Nishtar Road", "Old Sukkur", "Panjrapole", "Professors Colony",
        "Public School Area", "Queens Road", "Railway Colony", "Rohan Pura", "Sarafa Bazaar", "Shahi Bazaar",
        "Shikarpur Road", "Site Area Sukkur", "Station Road", "Sukkur IBA Campus Area", "Workshop Road"
    ],
    "Mardan": [
        "Baghdad Colony", "Bagh-e-Iram", "Bakshali Road", "Bank Road", "Bicket Gunj", "Canal Road",
        "Cantt (Mardan Cantonment)", "Charsadda Road", "Chumtarr", "College Road", "Dharmsala", "Dost Muhammad Colony",
        "Fatima Jinnah Colony", "Gaju Khan Chowk Area", "General Bus Stand Area", "Gujjar Garhi", "Hoti",
        "Industrial Estate", "Jalala Road", "Katlang Road", "Khadagzai", "Liaquat Park Area", "Malakand Road",
        "Mayar", "Muqam Road", "Nowshera Road", "Par Hoti", "Par Hoti Bazaar", "Rashakai Road", "Rifaqat Colony",
        "Rustam Road", "Saddar Bazaar Cantt", "Sardar Garhi", "Sarfaraz Gunj", "Sector A to E (Sheikh Maltoon Town)",
        "Shamsi Road", "Sheikh Maltoon Town", "Sugar Mills Area", "Swabi Road", "Takht Bhai Road", "Taru Jabba Road"
    ],
    "Abbottabad": [
        "Bara Hotar", "Banda Amlok", "Banda Jalal Khan", "Banda Sahib Khan", "Bilal Town", "Bypass Road",
        "Cantonment (Cantt)", "Comsats Campus Area", "Dhamtour", "Fawara Chowk Area", "Habibullah Colony",
        "Havelian Road", "Ilyasi Mosque Area", "Jabri", "Jangi", "Jinnahabad", "Kakul (PMA Road)", "Kehal",
        "Link Road", "Main Bazaar", "Malikpura", "Mandian", "Mirpur", "Murree Road", "Nariyan",
        "Nawan Shehr", "Old Abbottabad", "Pine Hills Colony", "PMA Road", "Public School Road", "Rakh Dhanni",
        "Reporters Colony", "Saddar Bazaar", "Salhad", "Sarban Colony", "Shahrah-e-Resham", "Sheikh-ul-Bandi",
        "Supply (Mansehra Road)", "Thandiani Road", "Upper Kehal"
    ],
    "Swat": [
        "Ahingaro Dherai", "Amankot", "Babuzai", "Balogram", "Barikot", "Bazaar Mingora", "College Colony",
        "Dangram", "Enzaro Dherai", "Faizabad", "Gulkada (1, 2, 3)", "Haji Baba", "Islampur", "Kabal",
        "Kanju (Township)", "Kokarai", "Landikas", "Malook Abad", "Marghazar Road", "Mian Baba", "Mingora City",
        "Mula Baba", "Nawakalay", "Panr", "Qambar", "Rahimabad", "Saidu Sharif", "Shahdara", "Sooray",
        "Spal Bandai", "Takhta Band", "Tindodag", "Watkay"
    ],
    "Muzaffarabad": [
        "Airport Area", "Al-Makki Colony", "Ambore", "Bank Road", "Basnara", "Bait-ul-Maal Colony", "Bilal Colony",
        "Chattar Domel", "Chehla Bandi", "CMH Area", "Domel (Sangam)", "Eidgah Road", "Ghari Pan",
        "Gojra (Bypass)", "Kallar Syedan", "Kamsar", "Lower Chattar", "Madina Market", "Main Bazaar",
        "Makri", "Mirwaiz Colony", "Naluchi", "Neelam Valley Road", "Plate Area", "Police Line Area",
        "Rahimkot", "Rathoi", "Rawalpindi Road", "Saddar Bazaar", "Saheli Sarkar Area", "Sarai Bandi",
        "Secretariat Area", "Shoukat Lines", "Subri", "Tariqabad", "Upper Chattar", "Upper Plate", "Wapda Colony"
    ],
    "Mirpur": [
        "Allama Iqbal Road", "Bank Hurmat", "Bradford Chowk Area", "Cantt Area", "Chaksawari Road", "Chechian",
        "Chowk Shaheedan", "Degree College Area", "DHA Mirpur", "F/1", "F/2", "F/3", "F/4", "G/1", "G/2", "G/3",
        "G/4", "Industrial Estate", "Islamgarh Road", "Jari Kas", "Kalyal", "Khadan", "Kotli Road", "Main Bazaar",
        "Mangla Road", "Mian Muhammad Town", "Model Town", "Naangi", "New City (Sector B1 to B4)", "New Mirpur City",
        "Officers Colony", "Paniola Road", "Pullo Manda", "Quaid-e-Azam Stadium Area", "Sector A1 to A4",
        "Sector B1 to B4", "Sector C1 to C4", "Sector D1 to D4", "Stadium Road", "Thothal"
    ],
    "Gilgit": [
        "Airport Area", "Amphary", "Basin", "Barmas", "Cantt (Gilgit Cantonment)", "Chilas Road", "Cinema Bazaar",
        "Danyore", "Domeal", "Farhad Colony", "Fiaz Abad", "Garibagh", "Gari Bagh", "Henzal", "Hospital Road",
        "Jalalabad", "Jutial (Cantt / Jutial Khari)", "Kashrote", "Khomer", "Konodas", "Main Bazaar",
        "Madina Colony", "Nagar Colony", "Nomal Road", "Nowshera", "Oshikhandass", "Pul Chowk Area",
        "Radio Pakistan Colony", "River View Road", "Sadpara Colony", "Sakwar", "Shahrah-e-Quaid-e-Azam",
        "Shahrah-e-Resham (KKH)", "Sonikot", "Takht-e-Sulaiman Area", "University Road", "Zulfiqarabad"
    ],
    "Wah Cantt": [
        "Aslam Market", "Babu Mohalla", "Basti", "Cantt Area", "CB Colony", "Chachi Mohalla", "Education City",
        "Farooq Colony", "G.T. Road Wah", "Gudwal", "Gulshan Colony", "Heavy Industries Taxila Area",
        "Hospital Road", "Lala Rukh (Colony)", "Lalarukh Block A to D", "Lalazar Colony", "Lossar",
        "Main Commercial Area", "Model Town", "Munirabad", "Muslim Nagar", "Nawababad", "Officers Colony",
        "POF Colony (Sector 1 to 24)", "Saddar Bazaar", "Sanitary Colony", "Shahwali Colony", "Station Road",
        "Target Range Area", "Timber Market", "Upper Cap Area"
    ],
    "Taxila": [
        "Ahatta", "Bhir Mound Area", "Cantt Area", "City Center", "Civil Hospital Area", "Engineers Colony",
        "Faisal Shaheed Road", "G.T. Road Taxila", "Gulshan-e-Saeed", "Heavy Mechanical Complex (HMC) Colony",
        "HIT Colony", "Industrial Estate", "Jinnah Colony", "Kohsar Colony", "Main Bazaar", "Model Town",
        "Museum Road", "Muslim Colony", "Nawababad", "Officers Colony", "Peshawar Road", "Railway Station Road",
        "Sarai Khola", "Sir Syed Colony", "Station Road", "Timber Market", "UET Taxila Campus Area", "Ziarat Area"
    ],
    "Sheikhupura": [
        "Al-Rehman Garden Sheikhupura", "Allama Iqbal Park Area", "Bhatti Colony", "Bhikhi Road", "Cantt Area",
        "Chak 12", "Chowk Pir Bahar Shah", "Civil Lines", "Company Bagh Area", "Distt Courts Area",
        "Farooqabad Road", "Ferozewala Road", "G.T. Road", "General Bus Stand Area", "Ghang Road", "Gulshan Colony",
        "Hiran Minar Road", "Housing Colony No. 1", "Housing Colony No. 2", "Jandiala Road", "Jinnah Park Area",
        "Kala Shah Kaku Area", "Lahore Road", "Main Bazaar", "Mandiali", "Model Town", "Mujahid Nagar",
        "Nabi Pura", "Old City", "Quaid-e-Azam Colony", "Railway Colony", "Rasool Nagar", "Sargodha Road",
        "Sharqpur Road", "Stadium Road", "Tariq Road", "Virk Colony", "Wapda Colony"
    ],
    "Kasur": [
        "Adalat Garh", "Babu Gulab Shah", "Baghban Pura", "Balu", "Basti Chiraghdin", "Chah Fatehdin",
        "Circular Road", "Civil Lines", "District Courts Area", "Ferozepur Road", "General Bus Stand Area",
        "Ghani Colony", "Gordhanpura", "Gulberg Colony", "Kot Azim Khan", "Kot Ghulam Muhammad",
        "Kot Halim Khan", "Kot Murad Khan", "Kot Usman Khan", "Kutchery Road", "Liaquat Colony", "Main Bazaar",
        "Model Town", "Munir Shaheed Colony", "Niazi Colony", "Old City", "Railway Road", "Raja Jang Road",
        "Rukan Pura", "Saddar Bazaar", "Shahbaz Khan Colony", "Steel Bagh", "Tariq Shaheed Colony", "Tibba Kambohan"
    ],
    "Gujrat": [
        "Ali Pura", "Bhimber Road", "Bolay", "Circular Road", "Civil Lines", "Courts Area", "Dawood Colony",
        "Faisal Colony", "G.T. Road Gujrat", "Gharibpura", "Gulberg Colony", "Hassan Chowk Area", "Industrial Area",
        "Jalalpur Jattan Road", "Jinnah Colony", "Kalra", "Kunjah Road", "Madina Colony", "Main Bazaar",
        "Mehmood Abad", "Model Town", "Muslim Town", "Nawabzada Road", "Old City", "Prince Chowk Area",
        "Railway Road", "Ram Pyari Mahal Area", "Rehman Shaheed Road", "Saddar Bazaar", "Sargodha Road",
        "Satellite Town", "Shadman Colony", "Shah Faisal Gate", "Shah Hussain", "Shadiwal Road", "University of Gujrat Campus"
    ],
    "Sahiwal": [
        "Adda Mai", "Al-Raza Colony", "Arifwala Road", "Baber Colony", "Bungla Road", "Canal Colony",
        "Chak 82/6R", "Chak 89/6R", "Circular Road", "Civil Lines", "College Town", "Commercial Area",
        "District Courts Area", "Farid Town", "Fateh Sher Colony", "General Bus Stand Area", "Ghala Mandi",
        "Gulshan-e-Noor", "Jail Road", "Jinnah Town", "Lahore-Multan Road", "Liaquat Bazaar", "Madina Colony",
        "Main Bazaar", "Model Town", "Muhammad Nagar", "Mujahid Colony", "New Sahiwal", "Noor Shah Road",
        "Old Harappa Road", "Pakpattan Road", "Police Lines Area", "Railway Colony", "Saddar Bazaar",
        "Satellite Town", "Shadman Town", "Stadium Road", "Tariq Bin Ziyad Colony", "Zaheer Nagar"
    ],
    "Okara": [
        "Akbar Road", "Al-Jannat Colony", "Allama Iqbal Town", "Benazir Colony", "Canal View", "Cantt (Okara Cantonment)",
        "Chak 4/4L", "Chak 53/2L", "Chak 54/2L", "Circular Road", "Civil Lines", "Depalpur Chowk Area",
        "Depalpur Road", "Faisal Colony", "Faisalabad Road", "General Bus Stand Area", "Gole Chowk Area",
        "Gulshan-e-Iqbal", "Housing Colony (1, 2, 3)", "Javed Colony", "Jinnah Colony", "Katchery Road",
        "Madina Colony", "Main Bazaar", "Mandi Road", "Mehboob Colony", "Model Town", "Mujahid Colony",
        "Muslim Town", "National Highway (G.T. Road)", "Old City", "Peoples Colony", "Police Lines Area",
        "Railway Road", "Ravi Road", "Rehman City", "Saddar Bazaar", "Samad Pura", "Shams Colony", "Stadium Road"
    ],
    "Rahim Yar Khan": [
        "Abbasia Town", "Abu Dhabi Road", "Al-Mustafa Town", "Airport Area", "Airport Road", "Baghdad Colony",
        "Canal Colony", "Chak 72/NP", "Circular Road", "City Center", "Civil Lines", "Commercial Market",
        "DHA Rahim Yar Khan", "Factory Area", "Gulberg Town", "Gulshan-e-Iqbal", "Gulshan-e-Osman",
        "Industrial Area", "Jail Road", "Jinnah Colony", "Kachehri Road", "Khanpur Road", "Liaquatpur Road",
        "Madina Colony", "Main Bazaar", "Model Town", "Mubarakpur Road", "Muhammadia Colony", "Muslim Town",
        "National Highway Road", "New Sadiq Colony", "Officers Colony", "Old City", "Railway Colony",
        "Saddar Bazaar", "Sadiq Colony", "Satellite Town", "Shahbaz Pur Road", "Stadium Road", "Tariqabad", "Trust Colony"
    ],
    "Jhelum": [
        "Bilal Town", "Cantt (Jhelum Cantonment)", "Chishtian Mohalla", "Circular Road", "Civil Lines",
        "District Complex Area", "G.T. Road Jhelum", "Ghaffar Town", "Gulshan Town", "Islam Pura",
        "Jada", "Kala Gujran", "Karimpura", "Kazimabad", "Khawaja Colony", "Machine Mohalla (1, 2, 3)",
        "Madina Town", "Main Bazaar", "Mangla Dam Road", "Model Town", "Mohalla Begumpura", "Mohalla Islampura",
        "Mohalla Saidan", "Mujahidabad", "Muslim Town", "Naya Mohalla", "Old City", "Officers Colony",
        "Peshawar Road", "Purana Mohalla", "Railway Colony", "Rathian", "River Bank Road", "Saddar Bazaar",
        "Satellite Town", "Shandar Chowk Area", "Soldier Market", "Stadium Road", "Tahlianwala", "Wapda Colony"
    ],
    "Dera Ghazi Khan": [
        "Al-Ghazi Town", "Al-Madina Colony", "Block 1 to 24", "Cantt Area", "Chah Pehalwan", "Circular Road",
        "Civil Lines", "College Road", "DG Cement Road", "Faridi Bazar", "Gadhai", "General Bus Stand Area",
        "Gulshan-e-Iqbal", "Housing Scheme (1, 2)", "Indus Highway", "Jampur Road", "Jinnah Colony",
        "Katchery Road", "Khyaban-e-Sarwar", "Kot Chutta Road", "Kotla Shikani", "Liaquat Town", "Madina Colony",
        "Main Bazaar", "Model Town", "Multan Road", "New Housing Scheme", "Officers Colony", "Old City",
        "Pathan Colony", "Pir Qattal Road", "Pull Chorrah", "Railway Colony", "Rajanpur Road", "Saddar Bazaar",
        "Sakhi Sarwar Road", "Samina Chowk Area", "Shadman Town", "Shah Sikandar", "Taunsa Road", "Vital Colony"
    ],
    "Dera Ismail Khan": [
        "Adda Daraban", "Airport Area", "Al-Qaim Colony", "Bannu Road", "Basti Ustrana", "Cantt (DI Khan Cantonment)",
        "Circular Road", "Civil Lines", "Commissionery Bazaar", "Degree College Area", "Dera Town", "Eidgah Road",
        "Gomal University Campus", "Grid Station Road", "Gulshan-e-Habib", "Haq Nawaz Park Area", "Indus River Road",
        "Islamia Colony", "Kanwer Ram Road", "Khyber Bazaar", "Kotli Imam Hussain", "Main Bazaar", "Mandi Road",
        "Moaza Dinpur", "Model Town", "Muslim Bazaar", "Naqvi Town", "Paroa Road", "Peshawar Road",
        "Qasba Shorkot", "Rahim Bazaar", "Railway Colony", "Ratta Kulachi", "Saddar Bazaar", "Shorkot Road",
        "Stadium Road", "Tank Road", "Topanwala Chowk Area", "Town Hall Area", "University Road", "Zafar Colony"
    ],
    "Kohat": [
        "Babri Banda", "Bannu Road", "Basti Fateh Khan", "Bazaar Pukhta", "Cantt (Kohat Cantonment)", "Chashma Road",
        "City Center", "Civil Lines", "Company Bagh Area", "Dhoda Road", "G.T. Road", "Gari Mawaz Khan",
        "Gulshan Colony", "Hangu Road", "Industrial Estate", "Jangle Khel", "Jannat Town", "KDA Kohat (Phase 1, 2)",
        "KDA Sector 1 to 9", "KDA Town", "Kharmatu", "Lachi Road", "Main Bazaar", "Mian Khel", "Mir Bash Khel",
        "Model Town", "Muhammad Zai", "Nawa Kali", "Officers Colony", "Peshawar Road", "Pir Khel", "Piro Khel",
        "Pukhta Bazaar", "Railway Colony", "Rawalpindi Road", "Saddar Bazaar Cantt", "Shahpur", "Shakardarra Road", "Tanda Dam Road"
    ],
    "Bannu": [
        "Airport Road", "Bannu City", "Bannu Township (Sector A to E)", "Bazaar Ahmad Khan", "Cantt (Bannu Cantonment)",
        "Chai Bazaar", "Circular Road", "Civil Lines", "Curzon Bridge Area", "Dera Road", "Ghari Sher Ahmad",
        "Ghoriwala Road", "Government College Area", "Hawed Road", "Industrial Area", "Kakki Road", "Kotka Allahdad",
        "Kotka Habibullah", "Kotka Pir Dil", "Lakki Gate", "Main Bazaar", "Mandi Bazaar", "Miran Shah Road",
        "Miryan Road", "Model Town", "Naurang Road", "Officers Colony", "Parachinar Road", "Qasba Road",
        "Railway Colony", "Saddar Bazaar", "Shahbaz Azmat Khel", "Shikari Bazaar", "Sikandar Khel", "Tanchi Bazaar"
    ]
};

        var standardTownAreas = [
            "Allama Iqbal Colony",
            "Bypass Road",
            "Cantt / Cantonment Area",
            "Circular Road",
            "Civil Lines",
            "College Road",
            "Commercial Area / Main Bazaar",
            "Courts / Kutchery Road",
            "General Bus Stand / Adda Road",
            "G.T. Road / Highway Bypass",
            "Gulberg / Gulshan Colony",
            "Hospital / DHQ / THQ Road",
            "Housing Colony / Scheme",
            "Industrial Area",
            "Jinnah Colony / Town",
            "Madina Colony",
            "Main Bazaar / City Center",
            "Model Town",
            "Muslim Town",
            "Officers Colony",
            "Old City / Androon Shehr",
            "Railway Station Road",
            "Saddar Bazaar",
            "Satellite Town",
            "Wapda Colony"
        ];

        function $id(id) { return document.getElementById(id); }

        function sortAZ(list) {
            return list.slice().sort(function(a, b) {
                return a.localeCompare(b, 'en', { sensitivity: 'base', numeric: true });
            });
        }

        function getAreasForCity(cityName) {
            if (!cityName) return [];
            cityName = cityName.trim();

            // Check if exact match or case-insensitive match in specific areas
            if (pakistanSpecificCityAreas[cityName]) {
                return sortAZ(pakistanSpecificCityAreas[cityName]);
            }
            for (var key in pakistanSpecificCityAreas) {
                if (pakistanSpecificCityAreas.hasOwnProperty(key)) {
                    if (key.toLowerCase() === cityName.toLowerCase()) {
                        return sortAZ(pakistanSpecificCityAreas[key]);
                    }
                }
            }

            // Generate localized authentic areas for this city/town
            var localized = [
                cityName + " Cantt / Cantonment",
                cityName + " City Center / Main Bazaar",
                cityName + " Civil Lines",
                cityName + " College Road",
                cityName + " Courts / Kutchery Road",
                cityName + " General Bus Stand / Adda",
                cityName + " G.T. Road / Highway Bypass",
                cityName + " Hospital / DHQ Road",
                cityName + " Housing Colony",
                cityName + " Industrial Area",
                cityName + " Jinnah Colony",
                cityName + " Model Town",
                cityName + " Muslim Town",
                cityName + " Officers Colony",
                cityName + " Old Town / Androon Shehr",
                cityName + " Railway Station Road",
                cityName + " Saddar Bazaar",
                cityName + " Satellite Town",
                cityName + " Wapda Colony"
            ];

            // Merge with standard town areas without duplicates
            var merged = {};
            for (var i = 0; i < localized.length; i++) {
                merged[localized[i]] = true;
            }
            for (var j = 0; j < standardTownAreas.length; j++) {
                merged[standardTownAreas[j]] = true;
            }

            var result = [];
            for (var areaName in merged) {
                if (merged.hasOwnProperty(areaName)) {
                    result.push(areaName);
                }
            }
            return sortAZ(result);
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

            var saved = preserve ? (area.hidden.value || area.select.getAttribute('data-selected') || '') : '';
            var cityValue = city.hidden ? city.hidden.value.trim() : '';

            if (!cityValue) {
                showSelectMode(area);
                area.select.innerHTML = '<option value="" disabled selected>Select City First</option>';
                area.select.disabled = true;
                area.hidden.value = '';
                return;
            }

            var list = getAreasForCity(cityValue);
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
                populateAreas(false);
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
