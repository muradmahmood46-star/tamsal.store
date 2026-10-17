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
            "Abbottabad": ["Banda Amlok","Banda Jalal Khan","Banda Sahib Khan","Bara Hotar","Bilal Town","Bypass Road","Cantonment (Cantt)","Comsats Campus Area","Dhamtour","Fawara Chowk Area","Habibullah Colony","Havelian Road","Ilyasi Mosque Area","Jabri","Jangi","Jinnahabad","Kakul (PMA Road)","Kehal","Link Road","Main Bazaar","Malikpura","Mandian","Mirpur","Murree Road","Nariyan","Nawan Shehr","Old Abbottabad","Pine Hills Colony","PMA Road","Public School Road","Rakh Dhanni","Reporters Colony","Saddar Bazaar","Salhad","Sarban Colony","Shahrah-e-Resham","Sheikh-ul-Bandi","Supply (Mansehra Road)","Thandiani Road","Upper Kehal"],
            "Ahmedpur East": ["Bahawalpur Road","Dera Nawab Sahib","Grain Market","Kutchery Road","Main Bazaar","Noor Mahal Road","Railway Station Area","Uch Sharif Road"],
            "Alipur": ["Chenab River Road","Jatoi Road","Main Bazaar","Muzaffargarh Road","Seetpur Road"],
            "Arifwala": ["Bahawalnagar Road","Burewala Road","College Road","Ghalla Mandi","Kutchery Road","Main Bazaar","Model Town","Sahiwal Road","Thana Bazaar"],
            "Attock": ["Attock Cantt","Civil Bazaar","Dar-ul-Islam Colony","Dhok Fateh","Fateh Jang Road","Gondal Road","Hazro Road","Kamra Road","Kutchery Road","Lawrencepur Area","Main Bazaar","Meena Bazaar","Mohalla Eidgah","People's Colony","Pleader Lane","Railway Colony","Sanjwal Cantt","Satellite Town","Shakar Dara"],
            "Badin": ["Cantt Area","Hyderabad Road","Kadhan Road","Main Bazaar","Matli Road","Model Town","Seerani Road"],
            "Bagh": ["Arja Road","Chikar Road","Housing Scheme","Kutchery Road","Main Bazaar","Rawalakot Road","Sudhan Gali Road"],
            "Bahawalnagar": ["Chishtian Road","City Center","Grain Market","Haroonabad Road","Jinnah Colony","Kutchery Road","Main Bazaar","Mandi Town","Minchinabad Road","Model Town","Railway Colony"],
            "Bahawalpur": ["Abbasia Town","Ahmedpur Road","Al-Haram Model Town","Al-Madina Colony","Baghdad-ul-Jadeed","Basti Dewan","Basti Ghafoor","Bindra Puli","Cantt (Bahawalpur Cantonment)","Circular Road","Civil Lines","Commercial Area","Darbar Mahal Area","DHA Bahawalpur (Sector A to N)","Dubai Mahal Road","Fareed Gate","General Bus Stand Area","Hashmi Garden","Hasilpur Road","Industrial Estate","Islamic Colony","Jhangiwala Road","Jinnah Colony","Khatm-e-Nabuwwat Chowk","Model Town A","Model Town B","Model Town C","Muhammadia Colony","Muslim Town","Noor Mahal Area","One Unit Staff Colony","Railway Colony","Saddar Bazaar Cantt","Saraiki Chowk Area","Satellite Town","Shadman City","Shahi Bazaar","Shikarpuri Gate","Trust Colony","University Town","Wapda Colony","Yazman Road"],
            "Bannu": ["Airport Road","Bannu City","Bannu Township (Sector A to E)","Bazaar Ahmad Khan","Cantt (Bannu Cantonment)","Chai Bazaar","Circular Road","Civil Lines","Curzon Bridge Area","Dera Road","Ghari Sher Ahmad","Ghoriwala Road","Government College Area","Hawed Road","Industrial Area","Kakki Road","Kotka Allahdad","Kotka Habibullah","Kotka Pir Dil","Lakki Gate","Main Bazaar","Mandi Bazaar","Miran Shah Road","Miryan Road","Model Town","Naurang Road","Officers Colony","Parachinar Road","Qasba Road","Railway Colony","Saddar Bazaar","Shahbaz Azmat Khel","Shikari Bazaar","Sikandar Khel","Tanchi Bazaar"],
            "Basirpur": ["Border Road","Depalpur Road","Grain Market","Main Bazaar","Mandi Ahmedabad Road"],
            "Batkhela": ["Chakdara Road","Main Bazaar","Malakand Road","Thana Road"],
            "Bhakkar": ["Behal Road","Bhakkar","Canal Colony","Civil Lines","Darya Khan","Darya Khan Road","Jhang Road","Kallur Kot Road","Main Bazaar","Mandi Town","Mankera Road","Model Town","Railway Road"],
            "Bhalwal": ["Main Bazaar","Mandi Bahauddin Road","Noon Sugar Mills Colony","Phularwan Road","Sargodha Road"],
            "Bhera": ["Circular Road","Main Bazaar","Motorway Interchange M-2","Phularwan Road","Sargodha Road"],
            "Bhimber": ["Barnala Road","Gujrat Road","Main Bazaar","Mirpur Road","Samahni Road"],
            "Burewala": ["Arif Bazaar","Canal Colony","Chichawatni Road","College Road","Gaggoo Mandi Road","Grain Market","Gulshan-e-Rehman","Housing Scheme","Luddan Road","Main Bazaar","Model Town","Multan Road","Satellite Town","Stadium Road","Vehari Road"],
            "Chakswari": ["Islamgarh Road","Kotli Road","Main Bazaar","Mirpur Road"],
            "Chakwal": ["Balkassar","Bhaun Road","Chakwal","Choa Saidan Shah Road","Choa Saiden Shah","City Center","Civil Lines","Dhudial","Islamia School Road","Kallar Kahar","Kallar Kahar Road","Line Park","Main Bazaar","Mian Mair","Mohalla Chappar","Mohalla Line Par","Odherwal","Pinwal","Railway Road","Rawalpindi Road","Talagang","Talagang Road","Thanil Kamal"],
            "Chaman": ["Boghra Road","Border Road","Mall Road","Railway Colony","Roghani Road","Taj Road"],
            "Charsadda": ["Farooq-e-Azam Chowk","Main Bazaar","Mardan Road","Nowshera Road","Peshawar Road","Prang","Rajjar","Sardaryab Area","Shabqadar","Tangi","Utmanzai"],
            "Chilas": ["Airport Area","Bhasha Dam Area","DC Colony","KKH Main Road","Main Bazaar","Shang Road"],
            "Chiniot": ["Bhowana Road","Chenab Nagar (Rabwah)","Circular Road","Faisalabad Road","Kutchery Road","Lahore Road","Main Bazaar","Mohalla Garha","Mohalla Qazi","Mohalla Rajanpur","Mohalla Usmanabad","Rail Bazaar","Railway Station Area","Sargodha Road","Thatha Muhammad Shah"],
            "Chishtian": ["Bahawalnagar Road","Chak 46\/3R","Ghalla Mandi","Hasilpur Road","Kutchery Road","Main Bazaar","Model Town","Mohalla Farooqabad","Railway Road","Sugar Mills Colony"],
            "Choa Saidan Shah": ["Chakwal Road","Kallar Kahar Road","Katas Raj Area","Khewra Road","Main Bazaar"],
            "Chunian": ["Allahabad Road","Civil Hospital Road","Kanganpur Road","Kutchery Road","Main Bazaar","Mohalla Kot Radha Kishan","Pattoki Road"],
            "Dadu": ["Indus Highway","Johi Road","Kutchery Road","Main Bazaar","Mehar Road","Radhan Road"],
            "Dadyal": ["Kotli Road","Main Bazaar","Mirpur Road","Rawalpindi Road"],
            "Daharki": ["Engro Colony","Fertilizer Factory Colony","Main Bazaar","Mari Petroleum Area","National Highway","Reti Road"],
            "Daska": ["Canal Colony","Civil Hospital Road","College Road","Gujranwala Road","Jamke Cheema Road","Kashmir Colony","Main Bazaar","Model Town","Mughal Pura","Pasrur Road","Sambrial Road"],
            "Dera Allah Yar": ["Jacobabad Road","Main Bazaar","Quetta Highway","Usta Muhammad Road"],
            "Dera Ghazi Khan": ["Al-Ghazi Town","Al-Madina Colony","Block 1 to 24","Cantt Area","Chah Pehalwan","Circular Road","Civil Lines","College Road","DG Cement Road","Faridi Bazar","Gadhai","General Bus Stand Area","Gulshan-e-Iqbal","Housing Scheme (1, 2)","Indus Highway","Jampur Road","Jinnah Colony","Katchery Road","Khyaban-e-Sarwar","Kot Chutta Road","Kotla Shikani","Liaquat Town","Madina Colony","Main Bazaar","Model Town","Multan Road","New Housing Scheme","Officers Colony","Old City","Pathan Colony","Pir Qattal Road","Pull Chorrah","Railway Colony","Rajanpur Road","Saddar Bazaar","Sakhi Sarwar Road","Samina Chowk Area","Shadman Town","Shah Sikandar","Taunsa Road","Vital Colony"],
            "Dera Ismail Khan": ["Adda Daraban","Airport Area","Al-Qaim Colony","Bannu Road","Basti Ustrana","Cantt (DI Khan Cantonment)","Circular Road","Civil Lines","Commissionery Bazaar","Degree College Area","Dera Town","Eidgah Road","Gomal University Campus","Grid Station Road","Gulshan-e-Habib","Haq Nawaz Park Area","Indus River Road","Islamia Colony","Kanwer Ram Road","Khyber Bazaar","Kotli Imam Hussain","Main Bazaar","Mandi Road","Moaza Dinpur","Model Town","Muslim Bazaar","Naqvi Town","Paroa Road","Peshawar Road","Qasba Shorkot","Rahim Bazaar","Railway Colony","Ratta Kulachi","Saddar Bazaar","Shorkot Road","Stadium Road","Tank Road","Topanwala Chowk Area","Town Hall Area","University Road","Zafar Colony"],
            "Dera Murad Jamali": ["Main Bazaar","National Highway","Rojhan Jamali Road","Temple Dera Road"],
            "Digri": ["Jhudo Road","Main Bazaar","Mirpur Khas Road","Tando Jan Mohammad Road"],
            "Dina": ["G.T. Road","Main Bazaar","Mangla Road","Railway Station Area","Rohtas Road"],
            "Dinga": ["Kharian Road","Main Bazaar","Mandi Bahauddin Road","Phalia Road","Railway Road"],
            "Dipalpur": ["Basirpur Road","Depalpur Chowk","Grain Market","Haveli Lakha Road","Kutchery Road","Main Bazaar","Model Town","Okara Road","Pakpattan Road"],
            "Dunyapur": ["Grain Market","Jahanian Road","Kahror Pakka Road","Lodhran Road","Main Bazaar","Railway Road"],
            "Faisalabad": ["16 JB","45 GB","59jb","66 GB","98\/GB","103 JB","103 RB","113 GB","117 JB","128 GB","189 RB","199 RB","203 RB","206 GB","207 GB","229 RB","248 RB","253 RB","257 RB","267 RB","271 RB","275 J.B","353 GB","433 GB","447 GB","482 GB","583 GB","Abdullah Garden","Abdullahpur","Al-Faisal Garden","Al-Najaf Colony","Ali Housing","Allama Iqbal Colony","Aminpur Bangla","Amin Town","Anand Pura","Ayub Agricultural Research Area","Ayub Agricultural Research Institute","Batala Colony","Bawa Chak","Bhaiwala","Board Of Education","Buchiana","Burj Mandi","Canal Colony","Canal Gardens","Canal Park","Canal Road","Chak 208 RB","Chak Jhumra Road","Chenab Club Area","Chiniot Bazaar","Citi Housing Faisalabad (Phase 1, 2)","Civil Lines","Clock Tower (Ghanta Ghar 8 Bazaars)","Clock Tower - Faisalabad","Daewoo Road","Dasuha","D Ground (Peoples Colony No. 1)","Dhudiwala","Dich Kot","Dijkot Road","Dogar Basti","Eden Garden","Eden Valley","Faisalabad","Faisal Garden","Faisal Town","Faizabad","FDA City","Gangapur","Garh","Gatwala","Ghulam Muhammad Abad","Gobind Pura","Green Town","Gulbahar Colony","Gulberg (A, B, C)","Gulistan Colony (1, 2)","Gulshan Colony","Guru Nanak Pura","Haji Abad","Harcharan Pura","Hasan Town","Hurmoy","Ideal Colony","Industrial Estate","Iqbal Stadium Area","Islam Nagar","Jail Road","Jamilabad","Jaranwala","Jaranwala Road","Jhang Bazaar","Jhang Road","Jhumra","Jinnah Colony","Kamal Town","Kanjwani","Karkhana Bazaar","Kashmir Road","Katchery Bazaar","Khayaban Colony (1, 2, 3)","Khurrian Wala","Kohinoor City","Kohinoor Flats","Kotwali Road","Lasani Garden","Lasani Town","Liaquat Town","Lorry Adda Area","Lyallpur Galleria Area","Madina Town","Mai Di Jhugi","Mall Road","Mamu Kanjan","Manawala","Mansoorabad","Marzi Pura","Millat Colony","Millat Road","Millat Town","Model City (1, 2)","Model Town","Montgomery Bazaar","Motorway City","Muhammad Nagar","Mujahid Colony","Murid Wala","Muslim Town","Muzaffar Colony","Nagina Colony","Naithan Pura","Nisar Colony","Nishatabad","Nishat Abad","Noorpur","Novelty Chowk Area","Officers Colony (1, 2)","P.A.F. Risalay Wala","Peoples Colony No. 1","Peoples Colony No.1","Peoples Colony No. 2","Peoples Colony No.2","Pindi Shiekh Musa","Rail Bazaar","Railway Road","Raza Abad","Razabad","Regency Plaza Area","Rex City","Rex City Area","Rorala Road","Sadiq Market","Saeedabad","Saeed Colony (1, 2)","Sahuwala","Saleemi Chowk Area","Samanabad","Samanabad - Faislabad","Samundri","Samundri Road","Sargodha Road","Satiana Road","Satiyana Road","Shadab Colony","Shahzad Colony","Sheikhupura Road","Sitara Colony","Sitara Sapna City","Susan Road","Tandlian Wala","Tariqabad","Tech Town","Thjkri Wala","Timber Market","University of Agriculture Campus","Wapda City","Warispura","Yousaf Abad"],
            "Fateh Jang": ["Bhatti Dhok","Islamabad Road","Kohat Road","Main Bazaar","Rawalpindi Road"],
            "Ferozewala": ["G.T. Road","Kala Shah Kaku Area","Lahore Bypass","Main Bazaar","Sheikhupura Road"],
            "Fort Abbas": ["Faqirwali Road","Haroonabad Road","Main Bazaar","Mandi Town","Marot Road"],
            "Gambat": ["GIMS Hospital Area","Khairpur Road","Main Bazaar","National Highway","Ranipur Road"],
            "Ghotki": ["Adilpur Road","Civil Hospital Area","Deviri Road","Main Bazaar","Mirpur Mathelo Road","National Highway","Station Road"],
            "Gilgit": ["Airport Area","Amphary","Barmas","Basin","Cantt (Gilgit Cantonment)","Chilas Road","Cinema Bazaar","Danyore","Domeal","Farhad Colony","Fiaz Abad","Garibagh","Gari Bagh","Henzal","Hospital Road","Jalalabad","Jutial (Cantt \/ Jutial Khari)","Kashrote","Khomer","Konodas","Madina Colony","Main Bazaar","Nagar Colony","Nomal Road","Nowshera","Oshikhandass","Pul Chowk Area","Radio Pakistan Colony","River View Road","Sadpara Colony","Sakwar","Shahrah-e-Quaid-e-Azam","Shahrah-e-Resham (KKH)","Sonikot","Takht-e-Sulaiman Area","University Road","Zulfiqarabad"],
            "Gojra": ["Eidgah Road","Faisalabad Road","Jhang Road","Main Bazaar","Mehdi Shah Colony","Model Town","Post Office Road","Samundri Road"],
            "Gujar Khan": ["Barki Road","Bewal Road","Civil Lines","G.T. Road","Kutchery Road","Main Bazaar","Mandra Road","Qazian Road","Railway Road","Sukho Road","Ward No. 1 to 10"],
            "Gujranwala": ["Al-Rehman Town","Alipur Chahta","Ali Town","Allah Rakha Town","Allama Iqbal Town","Aroop","Baghbanpura","Baghdad Town","Bakhtaywala","Bilal Town","Cantt (Gujranwala Cantonment)","Chan Da Qila","Chenab Gate","Citi Housing (Phase 1, 2)","Civil Lines","Climax Town","Commissioner Road","DC Colony","Delta Road","DHA Phase 2","Dhulley","Dusehra Ground Area","Eminabad Road","Faisal Town","Faiz Alam Town","Fareed Town","Fazal Town","Ferozewala Road","Fizaia Housing Scheme","Garjakh","Ghosia Colony","Ghulam Muhammad Town","Gondlanwala Road","Grand Trunk Road (G.T. Road)","Gujranwala","Gulabpura","Gulshan Colony","Gulzar Colony","Guru Nanak Pura","Hafizabad Road","Hajweri Town","Industrial Estate","Industrial Estate (1, 2)","Islam Pura","Jagatpura","Jalil Town","Jinnah Road","Judicial Housing Colony","Kacheri Road","Kamoke Road","Kangniwala","Khiali","Khiali Shahpur","Khurram Colony","Kotli Rustam","Ladhewala Waraich","Lalazar Colony","Liaquat Town","Lohianwala","Master City","Model Town","Mohafiz Town","Mughal Pura","Mujahid Pura","Muslim Town","Nandi Pur","Nowshera Road","Palm City","Pasrur Road","Peoples Colony","Qazi Town","Qila Didar Singh","Rachna Town","Rahwali Cantonments","Rahwali Cantt","Rajput Town","Rasheed Colony","Ratta Bajwa","Sadaat Town","Saddar Bazaar","Satellite Town","Satellite Town (Block A to D)","Shaheen Abad","Shalimar Town","Sheikhupura Road","Sialkot Bypass","Sialkot Road","Siddique Akbar Town","Sui Gas Office Area","Sulaiman Town","Tetley Road","Trust Colony","Wapda Town","Wapda Town (Phase 1, 2)"],
            "Gujrat": ["Ali Pura","Ali Town","Awan Town","Baghdad Colony","Banth","Bhimber Road","Bihar Colony","Bolay","Cheema Colony","Circular Road","Civil Lines","Courts Area","Dawood Colony","Dheerkay Khurd","Ellahi Colony","Faisal Colony","Faizabad","G.T. Road Gujrat","Garhi Ahmedabad","Gharibpura","Green Town","Gujarpur","Gujrat","Gulberg Colony","Gulshan-e-Ghulam Nabi","Haryawala","Hassan Chowk Area","Industrial Area","Islam Nagar","Jalalpur Jattan Road","Jamalpur Syedan","Jinnah Colony","Kalra","Khokar Colony","Kot Randhawa","Kunjah Road","Lala Musa","Lalwari Village","Madina Colony","Main Bazaar","Mehmood Abad","Model Town","Mohla Kalan","Murapur","Muslimabad","Muslimpura","Muslim Town","Nawabzada Road","Old City","Prince Chowk Area","Qadir Colony","Railway Road","Railway Road Area","Ram Pyari Mahal Area","Rehman Shaheed Road","Saddar Bazaar","Sargodha Road","Sargodha Road Area","Satellite Town","Shadiwal Road","Shadman Colony","Shadola Road Area","Shah Faisal Gate","Shah Hussain","Spall Town","Sultanabad","Tibbi Ghorian","University of Gujrat Campus"],
            "Gwadar": ["Airport Road","Jinnah Avenue","Koh-e-Batil","Marine Drive","New Town (Phase 1 to 5)","Padizar","Port Area","Sangar Housing Scheme","Shahi Bazaar"],
            "Hafizabad": ["Garhi Awan","Grain Market","Gujranwala Road","Hafizabad","Housing Colony","Jalalpur Road","Kassoki Road","Kutchery Road","Main Bazaar","Model Town","Pindi Bhattian","Pindi Bhattian Road","Railway Road","Sukheke Road"],
            "Haripur": ["Circular Road","G.T. Road","Hattar Industrial Estate","Khalabat Township","Kot Najibullah","Main Bazaar","Mohalla Afzalabad","Sera-e-Saleh","Shah Maqsood","Tarbela Road"],
            "Haroonabad": ["Bahawalnagar Road","Chak 1\/4L","Faqirwali Road","Fort Abbas Road","Ghalla Mandi","Housing Scheme","Kutchery Road","Main Bazaar","Model Town"],
            "Hasilpur": ["Bahawalpur Road","Chishtian Road","Grain Market","Main Bazaar","Model Town","Vehari Road"],
            "Hassan Abdal": ["Burhan Interchange Area","G.T. Road","Gurdwara Panja Sahib Area","Haripur Road","Main Bazaar"],
            "Havelian": ["Abbottabad Road","Haripur Road","Karakoram Highway","Main Bazaar","POF Havellian Area"],
            "Haveli Lakha": ["Depalpur Road","Grain Market","Hujra Road","Main Bazaar","Pakpattan Road"],
            "Hub": ["Allahabad","Civic Centre","Industrial Area Hub","Jam Ghulam Qadir Colony","Pathan Colony","RCD Highway","Sakran Road","Zehri Town"],
            "Hujra Shah Muqeem": ["Chunian Road","Dargah Area","Depalpur Road","Haveli Road","Main Bazaar"],
            "Hunza": ["Aliabad","Altit","Ganish","Ghalapan","Gulmit","Karimabad","Passu","Sost"],
            "Hyderabad": ["Abdullah Sports City","Al-Mustafa Town","Al-Rahim Town","Al Fazal Town","Amil Colony","Auto Bahn Road","Badin Stop Area","Bhittai Nagar","Bismillah City Phase 1","Cantonment","Cantt (Hyderabad Cantonment)","Choti Gitty","Citizen Colony","Civil Lines","Cloth Market Area","Daman-e-Kohsar","Defence Colony","Defence Housing Society","Doctor Colony","Faqir Ka Pir","Fateh Chowk","Gari Khata","Gidu Chowk Area","Goods Naka","Government College Area","Gulistan-e-Sajjad","Gulshan-e-Fatima","Gulshan-e-Hali","Gulshan-e-Hameed Ali Housing Scheme","Gulshan-e-Mehran","Gulshan-e-Sehar","Hala Naka","Hali Nagar","Happy Homes","Heerabad","Hirabad","Hussainabad","Hyderabad - Autobhan Road","Industrial Area","Isra University Area","Isra Village","Jail Road","Jamshoro Road","Kali Mori","Katchery Road","Kohsar","Kohsar Housing Scheme","Kotri Barrage Area","Labor Colony","Latifabad","Latifabad Unit 1","Latifabad Unit 2","Latifabad Unit 3","Latifabad Unit 4","Latifabad Unit 5","Latifabad Unit 6","Latifabad Unit 7","Latifabad Unit 8","Latifabad Unit 9","Latifabad Unit 10","Latifabad Unit 11","Latifabad Unit 12","Liaquat Colony","London Town","Madina Town","Makki Shah Road","Market Tower","Memon Society","Mirpurkhas Road","Model Town","Mubarak Colony","Muhammadi Town","Naseem Nagar","New Cloth Market","Niaz Stadium Area","Old City","Pakora Stop","Pareetabad","Pathan Colony","Phuleli","Phulleli","Poona Abad","PTCL Colony","Qasimabad","Qasimabad Phase 1","Qasimabad Phase 2","Railway Colony","Resham Gali","Rishi Ghat","Saddar","Saddar Bazaar","Sarfaraz Colony","Shahbaz Town","Shahi Bazaar","Shah Latif","Shah Latif Colony","Sindh University Society","Site Area","Solider Bazaar","State Life Colony","Station Road","Tando Agha","Tando Jam Road","Tando Tayyab","Thandi Sarak","Tilak Incline","Wadhu Wah Road"],
            "Islamabad": ["Airport Enclave","Ali Pur","B-17 Multi Gardens (Block A, B, C, D, E, F, G)","Bahria Enclave (Sector A to P)","Bahria Golf City","Bahria Town - Phase 7","Bahria Town Islamabad (Phase 2 to 9)","Bani Gala","Banni Gaala","Bhara Kahu","Blue Area","Burma Town","C-14","C-15","C-16","Chak Shahzad","Chatta Bakhtawar","Chattah Bakhtawar","D-12 (Sector 1, 2, 3, 4)","D-13","D-14","D-17 (Cabinet Division \/ MPCHS)","D-18","DHA - Phase 2","DHA Islamabad Phase 1 (Orchard)","DHA Islamabad Phase 2 (Sector A to V)","DHA Islamabad Phase 3","DHA Islamabad Phase 4","DHA Islamabad Phase 5","DHA Valley","Diplomatic Enclave","E-7","E - 7","E - 8","E-8 (Naval Complex)","E - 9","E-9 (Air Force Complex)","E-10","E - 10","E - 11","E-11 (Multi Gardens \/ FECHS \/ Sector 1 to 4)","E-12","E-16 (Cabinet Division)","E-17 (Cabinet Division)","F-5","F - 5","F - 6","F-6 (Super Market, Sector 1 to 4)","F - 7","F-7 (Jinnah Super, Sector 1 to 4)","F - 8","F-8 (Ayub Market, Sector 1 to 4)","F - 9","F-9 (Fatima Jinnah Park Area)","F - 10","F-10 (Markaz, Sector 1 to 4)","F - 11","F-11 (Markaz, Sector 1 to 4)","F-12","F - 15","F-15 (Khayaban-e-Kashmir)","F-17 (Tele Gardens)","Farash Town","G-5","G - 5","G - 6","G-6 (Melody Market \/ Aabpara)","G - 7","G-7 (Sitara Market)","G - 8","G-8 (Markaz \/ Sector 1 to 4)","G - 9","G-9 (Karachi Company, Markaz, Sector 1 to 4)","G - 10","G-10 (Markaz, Sector 1 to 4)","G - 11","G-11 (Markaz, Sector 1 to 4)","G-12","G - 12","G - 13","G-13 (Markaz, Sector 1 to 4)","G - 14","G-14 (Sector 1, 2, 3, 4)","G-15 (Jammu & Kashmir Society)","G-16","G-17","Ghauri Town","Ghauri Town (Phase 1 to 8)","Golra Sharif","Gulberg Greens (Executive, Farmhouses)","Gulberg Residencia (Block A to V)","H - 8","H-8 (Educational Sector)","H-9","H - 9","H - 10","H-10 (IIUI Campus Area)","H-11","H - 11","H - 12","H-12 (NUST Campus Area)","H-13","H - 13","Humak Model Town","I - 8","I-8 (Markaz, Sector 1 to 4)","I - 9","I-9 (Industrial Area, Sector 1 to 4)","I - 10","I-10 (Markaz, Sector 1 to 4)","I - 11","I-11 (Fruit Market Area)","I-12","I-14 (Riphah Campus Area)","I-15","I-16","I-17","Islamabad Expressway Area","Jinnah Garden (Phase 1, 2)","Kashmir Highway Area","Koral Chowk Area","Korang Town","Kuri Road","Kurri Road","Lehtrar Road","Loi Bher","Madina Town - Islamabad","Margalla Town (Phase 1, 2)","Media Town","Model Town Humak","Motorway City","National Police Foundation (O-9)","Naval Anchorage (Sector A to J)","New Islamabad Airport Area","Nilore","Pakistan Town","Pakistan Town (Phase 1, 2)","Park Enclave (Phase 1, 2, 3)","Park Road","Park View City (Block A to J)","PWD","PWD Housing Society","Rawal Dam Area","Rawal Town","Rawat Industrial Area","River Garden Housing Scheme","Sangjani","Sarai Kharbuza","Shah Allah Ditta","Shahzad Town","Shakar Parian","Sihala","Soan Garden (Block A to H)","Spring Valley","Swan Garden","Talai Kalan","Tarlai Kalan","Tarnol","Top City-1","Tramrri","University Town Islamabad","Wapda Colony","Zaraj Housing Society (Sector A, B, C)","Zero Point Islamabad","Zone 4","Zone 5"],
            "Jacobabad": ["Cantt Area","D.C. Road","Dasti Mohalla","Family Lines","Gharibabad","Main Bazaar","Quetta Road","Railway Colony","Shikarpur Road","Tower Road"],
            "Jahanian": ["Dunyapur Road","Grain Market","Khanewal Road","Lodhran Road","Main Bazaar","Multan Road","Railway Road"],
            "Jalalpur Jattan": ["Circular Road","Gujrat Road","Karianwala Road","Main Bazaar","Tanda Road"],
            "Jalalpur Pirwala": ["Main Bazaar","Multan Road","Shujaabad Road","Tarinda Road","Uch Sharif Road"],
            "Jamshoro": ["Barrage Road","Indus Highway","Main Bazaar","Mehran UET Campus","University of Sindh Campus"],
            "Jaranwala": ["Chamra Mandi","Faisalabad Road","Grain Market","Lahore Road","Main Bazaar","Model Town","Nankana Road","Satyana Road"],
            "Jatoi": ["Alipur Road","Main Bazaar","Muzaffargarh Road","Shah Jamal Road"],
            "Jhang": ["Ahmed Pur","Ayub Chowk","Basti Lal Dey","Chiniot","Chund Bharwana","Civil Lines","College Road","Faisalabad Road","Gojra Road","Jhang","Kot Lakhnana","Maghiana","Main Bazaar","Nawaz Chowk","Railway Station Area","Saddar","Satellite Town","Shaheed Chowk","Shorkot","Shorkot Road","Toba Road","Yousaf Shah Road"],
            "Jhelum": ["Army Quarters","Arsal Town","Bagh Mohalla","Bilal Town","Cantt (Jhelum Cantonment)","Chishtian Mohalla","Chittan","Circular Road","Civil Lines","Dhoke Firdous","District Complex Area","Fozila Colony","G.T. Road Jhelum","Ghaffar Town","Gul Afshan Colony","Gulshan Town","Gulzari Colony","Iqbal Colony","Islam Pura","Jada","Jhelum","Jinnah Colony","Kala Gujran","Karimpura","Kazimabad","Khawaja Colony","Lalazar Colony","Lota Village","Machine Mohalla (1, 2, 3)","Madina Town","Main Bazaar","Mangla Dam Road","Mehmoodabad","Model Colony","Model Town","Mohalla Begumpura","Mohalla Islampura","Mohalla Saidan","Mujahidabad","Mujahid Abad","Muslim Town","Nawab Colony","Naya Mohalla","Naya Mohallah Jhelum","New Christian Colony","Officers Colony","Old City","Peshawar Road","Purana Mohalla","Railway Colony","Rathian","River Bank Road","Saddar Bazaar","Satellite Town","Shadab Colony","Shamali Mohalla","Shandar Chowk Area","Soldier Market","Stadium Road","Tahlianwala","Wapda Colony","West Colony"],
            "Kabirwala": ["Jhang Road","Khanewal Road","Kutchery Road","Main Bazaar","Multan Road","Sarai Sidhu Road"],
            "Kahror Pakka": ["Dunyapur Road","Lodhran Road","Mailsi Road","Main Bazaar","Railway Road"],
            "Kahuta": ["Azad Kashmir Road","KRL Area","Main Bazaar","Rawalpindi Road"],
            "Kallar Kahar": ["Chakwal Road","Choa Road","Lake Area","Main Bazaar","Motorway M-2 Interchange"],
            "Kallar Syedan": ["Choa Khalsa Road","Dadyal Road","Main Bazaar","Rawalpindi Road"],
            "Kamalia": ["Chichawatni Road","Main Bazaar","Mohalla Fazal Shah","Railway Road","Rajana Road","Sugar Mills Colony","Toba Road"],
            "Kambar (Qambar)": ["Hamal Lake Road","Larkana Road","Main Bazaar","Shahdadkot Road"],
            "Kamoke": ["Chanda Singh","Civil Hospital Area","G.T. Road","Ghala Mandi","Main Bazaar","Mandi Road","Model Town","Railway Road","Raja Sadhoke","Tatley Aali Road","Tibba Shah Behlol"],
            "Kandhkot": ["Grain Market","Indus Highway","Jacobabad Road","Kashmore Road","Main Bazaar"],
            "Karachi": ["Abdullah Haroon Road","Abul Hassan Isphahani Road","Ahsanabad","Airport Area","Akhtar Colony","Al-Falah Society","Al-Hilal Society","Al-Noor Society","Amil Colony","Ancholi","Aram Bagh","Askari 1","Askari 2","Askari 3","Askari 4","Askari 5","Azam Basti","Azizabad","Bahadurabad","Bahria Town - Precinct 1","Bahria Town - Precinct 2","Bahria Town - Precinct 3","Bahria Town - Precinct 4","Bahria Town - Precinct 5","Bahria Town - Precinct 6","Bahria Town - Precinct 7","Bahria Town - Precinct 8","Bahria Town - Precinct 9","Bahria Town - Precinct 10","Bahria Town - Precinct 11","Bahria Town - Precinct 12","Bahria Town - Precinct 13","Bahria Town - Precinct 14","Bahria Town - Precinct 15","Bahria Town - Precinct 16","Bahria Town - Precinct 17","Bahria Town - Precinct 18","Bahria Town - Precinct 19","Bahria Town - Precinct 20","Bahria Town - Precinct 21","Bahria Town - Precinct 22","Bahria Town - Precinct 23","Bahria Town - Precinct 24","Bahria Town - Precinct 25","Bahria Town - Precinct 26","Bahria Town - Precinct 27","Bahria Town - Precinct 28","Bahria Town - Precinct 29","Bahria Town - Precinct 30","Bahria Town - Precinct 31","Bahria Town - Precinct 32","Bahria Town - Precinct 33","Bahria Town Karachi","Baldia Town","Baldiya Town","Baloch Colony","Banglore Town","Baraf Khana","Barkat-e-Hyderi","Bath Island","Bhimpura","Bin Qasim Town","Boat Basin","Buffer Zone (Sector 15-A, 15-B)","BufferZone - Sector 15 A 1","BufferZone - Sector 15 A 2","BufferZone - Sector 15 A 3","BufferZone - Sector 15 A 4","BufferZone - Sector 15 A 5","BufferZone - Sector 15 B","BufferZone - Sector 16 A","BufferZone - Sector 16 B","Burns Road","Cantonment","Cantt (Karachi Cantonment)","Cantt Station","Civil Line","Civil Lines","Clifton (Block 1 to 9)","Clifton - Block 1","Clifton - Block 2","Clifton - Block 3","Clifton - Block 4","Clifton - Block 5","Clifton - Block 6","Clifton - Block 7","Clifton - Block 8","Clifton - Block 9","Clifton - Delhi Colony","Clifton - Kehkashan","Clifton - Punjab Colony","Clifton - Shah Rasool Colony","Clifton - Zamzama","Cochinwala Market","Cosmopolitan Society","Dalmia","Darwaish Colony","Dastagir Colony","Defance View (Phase 1, 2)","Defence Garden","Defence View - Phase 1","Defence View - Phase 2","Delhi Colony","DHA - Phase 1","DHA - Phase 2","DHA - Phase 3","DHA - Phase 4","DHA - Phase 5","DHA - Phase 6","DHA - Phase 7","DHA - Phase 8","DHA - Phase 9","DHA City Karachi","DHA Phase 1","DHA Phase 2","DHA Phase 4","DHA Phase 5 (Ext)","DHA Phase 6","DHA Phase 7 (Ext)","DHA Phase 8","Dhoraji - Adamjee Nagar","Dhoraji - CP & Berar Society","Dhoraji Colony","Diamond City","Drigh Colony","Drigh Road","F.B Area - Azizabad","F.B Area - B1 Area","F.B Area - B Area","F.B Area - Block 1","F.B Area - Block 2","F.B Area - Block 3","F.B Area - Block 4","F.B Area - Block 5","F.B Area - Block 6","F.B Area - Block 8","F.B Area - Block 9","F.B Area - Block 10","F.B Area - Block 11","F.B Area - Block 12","F.B Area - Block 13","F.B Area - Block 14","F.B Area - Block 15","F.B Area - Block 16","F.B Area - Block 17","F.B Area - Block 18","F.B Area - Block 19","F.B Area - Block 20","F.B Area - Block 21","F.B Area - Block 22","F.B Area – Block 7","F.C Area - C1 Area","F.C Area - C Area","Falcon Complex","Faran Society","Fatima Jinnah Colony","FC Area","Federal B Area (Block 1 to 22)","Firdous Colony","Fowler Lines","Gadap Town","Garden - Garden East","Garden - Garden West","Garden - Soldier Bazaar","Garden East","Garden West","Gharibabad","Gizri","Golimar","Golra Sharif","Gulberg Town","Gulistan-e-Jauhar (Block 1 to 20)","Gulistan-e-Johar - Block 1","Gulistan-e-Johar - Block 2","Gulistan-e-Johar - Block 3","Gulistan-e-Johar - Block 4","Gulistan-e-Johar - Block 5","Gulistan-e-Johar - Block 6","Gulistan-e-Johar - Block 7","Gulistan-e-Johar - Block 8","Gulistan-e-Johar - Block 9","Gulistan-e-Johar - Block 10","Gulistan-e-Johar - Block 11","Gulistan-e-Johar - Block 12","Gulistan-e-Johar - Block 13","Gulistan-e-Johar - Block 14","Gulistan-e-Johar - Block 15","Gulistan-e-Johar - Block 16","Gulistan-e-Johar - Block 17","Gulistan-e-Johar - Block 18","Gulistan-e-Johar - Block 19","Gulistan-e-Johar - Block 20","Gulshan-e-Ameen","Gulshan-e-Faisal","Gulshan-e-Hadeed (Phase 1, 2)","Gulshan-e-Hadeed - Data Nagar","Gulshan-e-Hadeed - EIDU Goth","Gulshan-e-Hadeed - Gulshan-e-Mauzzam","Gulshan-e-Hadeed - Gulshan-e-Rehman","Gulshan-e-Hadeed - Mehran Road","Gulshan-e-Hadeed - Phase 1","Gulshan-e-Hadeed - Phase 2","Gulshan-e-Hadeed - Phase 3","Gulshan-e-Hadeed - PTCL Satellite Station","Gulshan-e-Hadeed - Shah Latif Town","Gulshan-e-Hadeed - Shahnawaz Goth","Gulshan-e-Hadeed - Shah Town","Gulshan-e-Hadeed - Steel Town","Gulshan-e-Hadeed - TCP Godowns","Gulshan-e-Iqbal (Block 1 to 19)","Gulshan-e-Iqbal - Adamjee Nagar","Gulshan-e-Iqbal - Block 1","Gulshan-e-Iqbal - Block 2","Gulshan-e-Iqbal - Block 3","Gulshan-e-Iqbal - Block 4","Gulshan-e-Iqbal - Block 5","Gulshan-e-Iqbal - Block 6","Gulshan-e-Iqbal - Block 7","Gulshan-e-Iqbal - Block 8","Gulshan-e-Iqbal - Block 9","Gulshan-e-Iqbal - Block 10","Gulshan-e-Iqbal - Block 11","Gulshan-e-Iqbal - Block 12","Gulshan-e-Iqbal - Block 13","Gulshan-e-Iqbal - Block 14","Gulshan-e-Iqbal - Block 15","Gulshan-e-Iqbal - Block 16","Gulshan-e-Iqbal - Block 17","Gulshan-e-Iqbal - Block 18","Gulshan-e-Iqbal - Block 19","Gulshan-e-Iqbal - Civic Center","Gulshan-e-Iqbal - Dhoraji","Gulshan-e-Jamal","Gulshan-e-Kaneez Fatima","Gulshan-e-Malir","Gulshan-e-Maymar","Gulshan-e-Maymar (Sector Q, R, S, T, U, V, W, X, Y, Z)","Gulshan-e-Osman","Gulshan-e-Rafi","Gulshan-e-Roomi","Gulshan-e-Shamim","Gulshan-e-Umair","Gulzar-e-Hijri","Gulzar-e-Hijri (Scheme 33)","Guru Mandir","Haidery (North Nazimabad Block G, H)","Hawkesbay","Hawksbay","Hill Park Area","Hub River Road","Hussainabad","I.I. Chundrigar Road","I.I Chundrigar","Jaffar-e-Tayyar Society","Jail Road - Hyderabad Colony","Jamshed Quarters","Jamshed Road","Jinnah Avenue","Jinnah International Airport","Jodia Bazaar","Jubilee Market Area","K.A.E.C.H.S","K.D.A Officers","K.D.A Scheme 1","Kalakot","Karachi Memon Society","Karachi University Campus","Karimabad","Karsaz","Kashmir Road","KDA Officers Housing Society","KDA Scheme 1","KDA Scheme 33","Kemari","Khadda Market","Kharadar","Khokrapar","Khudadad Colony","Khuldabad","Korangi - Abdullah Shah Noorani Pahari Colony","Korangi - Korangi Industrial Area","Korangi - Mehran Town","Korangi - Nasir Colony","Korangi - PAF Base Korangi Creek","Korangi - Sector 31 B","Korangi - Sector 31 D","Korangi - Sector 32 B","Korangi - Sector 41 B","Korangi - Zaman Town","Korangi - Zia Colony","Korangi Creek","Korangi Crossing","Korangi Industrial Area","Korangi Sector 1 to 51","Landhi - Alflah Housing Society","Landhi - Awami Colony","Landhi - Bagh-e-Korangi","Landhi - Bakhtawar Goth","Landhi - Barmi Colony","Landhi - Bhutto Nagar","Landhi - Future Colony","Landhi - Gulshan-e-Rafi","Landhi - Ilyas Goth","Landhi - Labour Colony","Landhi - Landhi Industrial Area","Landhi - Muslimabad Colony","Landhi - Muzaffarabad Colony","Landhi - Punjab Town","Landhi - Qasim Town","Landhi - Sadat Colony","Landhi - Shah Khalid Colony","Landhi - Sharafi Goth","Landhi - Zamanabad","Landhi Industrial Area","Landhi Sector 1 to 36","Landhi Town - 36 B","Liaquatabad (Lalukhet 1 to 10)","Liaquatabad - Block 1","Liaquatabad - Block 2","Liaquatabad - Block 3","Liaquatabad - Block 4","Liaquatabad - Block 5","Liaquatabad - Block 6","Liaquatabad - Block 7","Liaquatabad - Block 8","Liaquatabad - Block 9","Liaquatabad - Block 10","Lines Area","Liyari","Lyari Town","M.A. Jinnah Road","M.A Jinnah Rd","Macchar Colony","Madras Cooperative Housing Society","Mahmoodabad","Malir - Malir","Malir - Malir Cantt","Malir Cantt","Malir City","Malir Colony","Malir Halt","Malir Model Town","Manghopir","Manora","Manzoor Colony","Maripur","Martin Quarters","Meethadar","Mehmoodabad","Mehran Town","Metroville (SITE)","Model Colony","Mohammad Ali Memorial Society (MACHS)","Mohammad Ali Society","Mominabad","Mosamiyat","Muslimabad","Nagan Chowrangi","Nanak Wara","Napier Quarter","Naval Anchorage Karachi","Naval Colony (Haroon Bahria)","Nazimabad (Block 1 to 5)","Nazimabad - Block 1","Nazimabad - Block 2","Nazimabad - Block 3","Nazimabad - Block 4","Nazimabad - Block 5","New Chali","New Karachi","New Karachi (Sector 5-A to 11-L)","New Rizvia Society","New Surjani","NIPA Chowrangi Area","North Karachi (Sector 7 to 11)","North Karachi  - Sector 2","North Karachi  - Sector 3","North Karachi  - Sector 4","North Karachi  - Sector 5 - A 1","North Karachi  - Sector 5 - A 2","North Karachi  - Sector 5 - A 3","North Karachi  - Sector 5 - A 4","North Karachi  - Sector 5 - B 1","North Karachi  - Sector 5 - B 2","North Karachi  - Sector 5 - B 3","North Karachi  - Sector 5 - B 4","North Karachi  - Sector 5 - C 1","North Karachi  - Sector 5 - C 2","North Karachi  - Sector 5 - C 3","North Karachi  - Sector 5 - C 4","North Karachi  - Sector 5 - I","North Karachi  - Sector 5 - J","North Karachi  - Sector 5 - K","North Karachi  - Sector 5 - L","North Karachi  - Sector 5 - M","North Karachi  - Sector 6","North Karachi  - Sector 7 - D 1","North Karachi  - Sector 7 - D 2","North Karachi  - Sector 7 - D 3","North Karachi  - Sector 7 - D 4","North Karachi  - Sector 8","North Karachi  - Sector 9","North Karachi  - Sector 10","North Karachi  - Sector 11 - A","North Karachi  - Sector 11 - B","North Karachi  - Sector 11 - C 1","North Karachi  - Sector 11 - C 2","North Karachi  - Sector 11 - C 3","North Karachi  - Sector 11 - E","North Karachi  - Sector 11 - H","North Karachi  - Sector 11 - I","North Karachi  - Sector 11 - K","North Karachi  - Sector 11 - L","North Karachi  - Sector 12 A","North Karachi  - Sector 12 C","North Nazimabad (Block A to W)","North Nazimabad - Block A","North Nazimabad - Block B","North Nazimabad - Block C","North Nazimabad - Block D","North Nazimabad - Block E","North Nazimabad - Block F","North Nazimabad - Block G","North Nazimabad - Block H","North Nazimabad - Block I","North Nazimabad - Block J","North Nazimabad - Block K","North Nazimabad - Block L","North Nazimabad - Block M","North Nazimabad - Block N","North Nazimabad - Block O","North Nazimabad - Block P","North Nazimabad - Block Q","North Nazimabad - Block R","North Nazimabad - Block S","North Nazimabad - Block T","North Nazimabad - Block W","Nursery (PECHS Block 6)","Old Clifton","Old Golimar","Old Town - Bhimpora","Old Town - Bohra Pir","Old Town - Bombay Bazar","Old Town - Jodia Bazar","Old Town - Kagzi Bazar","Old Town - Kakri Ground","Old Town - Kamil Gali","Old Town - Khada Market","Old Town - Kharadar","Old Town - Lee Market","Old Town - Mithadar","Old Town - Nanwara","Old Town - Nishter Road","Old Town - Pan Mandi","Old Town - Ramswami","Old Town - Ranchorline","Orangi Town (Sector 1 to 16)","Orangi Town - Banaras Town","Orangi Town - Bangla Bazaar","Orangi Town - Bilal Colony","Orangi Town - Katti Pahari","Orangi Town - Moria Goth Orangi","Orangi Town - Orangi","Orangi Town - Sector 14 - A","Orangi Town - Sector 14 - C","Orangi Town - Thorani Goth","P.E.C.H.S - Block 1","P.E.C.H.S - Block 2","P.E.C.H.S - Block 3","P.E.C.H.S - Block 4","P.E.C.H.S - Block 5","P.E.C.H.S - Block 6","P.E.C.H.S - Block 7","P.E.C.H.S - Khalid Bin Walid","P.E.C.H.S - Tariq Road","PAF Base Faisal Area","PAF Base Korangi","PAF Base Masroor","Pakistan Chowk","Pankha Hotel Area","Parsi Colony","Patel Para","PECHS (Block 2, 3, 6)","PIB Colony","PIDC House Area","Pipri Goth","Pir Illahi Buksh Colony","Port Qasim Area","Punjab Colony","Qasba Colony","Qayyumabad","Quaidabad","Ranchore Line","Rashid Minhas Road","Regal Chowk Area","Rizvia Society","S.I.T.E - Golimar","S.I.T.E - S.I.T.E","Saadi Town","Saddar","Saddar Bazaar","Saeedabad","Safoora Goth","Sakhi Hassan","Scheme 33","Scheme 33 (Sector 17-A to 52)","Sea View","Secunderabad","Shabbirabad","Shadman Town","Shaheed-e-Millat Road","Shah Faisal Colony (Block 1 to 5)","Shah Faisal Colony - 3","Shah Faisal Colony - 4","Shah Faisal Colony - Aswan Town","Shah Faisal Colony - Green Town","Shah Faisal Colony - Gulshan-e-Asghar","Shah Faisal Colony - Muslimabad Malir City","Shah Faisal Colony - Shah Faisal Colony 1","Shah Faisal Colony - Shah Faisal Colony 5","Shah Faisal Colony - Shamsi Society","Shahra-e-Faisal - Jinnah Housing Society","Shahra-e-Faisal - Umar Colony","Shahrah-e-Faisal","Shahrah-e-Faisal Area","Shahrah-e-Liaquat","Shahrah-e-Pakistan","Shanti Nagar","Sharfabad","Sharfabad Society","Sher Shah Colony","Sindhi Muslim Cooperative Housing Society","Sindhi Muslim Housing Society (SMCHS)","SITE Area","Sohrab Goth","Soldier Bazaar (1, 2, 3)","Soldier Bazar","State Life Society","Steel Town","Surjani Town (Sector 4 to 10)","Tariq Road","Teen Hatti","Tipu Sultan Road","Tower (Merewether Tower)","University Road","Urdu Bazaar","Usmanabad","Water Pump (Federal B Area Block 16)","West Wharf","Yaseenabad","Zamzama"],
            "Kashmore": ["Guddu Barrage Road","Indus Highway","Kandhkot Road","Main Bazaar","Punjab Border Area"],
            "Kasur": ["Adalat Garh","Babu Gulab Shah","Baghban Pura","Balu","Basti Chiraghdin","Chah Fatehdin","Chunian","Circular Road","Civil Lines","District Courts Area","Ferozepur Road","General Bus Stand Area","Ghani Colony","Gordhanpura","Gulberg Colony","Gulberg Colony - Kasur","Kasur","Khudian Khas","Kot Azim Khan","Kot Ghulam Muhammad","Kot Halim Khan","Kot Murad Khan","Kot Usman Khan","Kutchery Road","Liaquat Colony","Main Bazaar","Model Town","Munir Shaheed Colony","Niazi Colony","Old City","Pattoki","Railway Road","Raja Jang Road","Rukan Pura","Saddar Bazaar","Shahbaz Khan Colony","Steel Bagh","Tariq Shaheed Colony","Tibba Kambohan"],
            "Khairpur": ["Babarloi","Faiz Mahal Area","Gambat Road","Kot Diji Road","Luqman","Main Bazaar","Mall Road","Pir Jo Goth","Shah Abdul Latif University Area","Station Road"],
            "Khairpur Tamewali": ["Bahawalpur Road","Hasilpur Road","Lal Sohanra Road","Main Bazaar"],
            "Khanewal": ["Block 1 to 12","Civil Lines","College Road","Dunyapur Road","Jahanian","Jahanian Road","Kabirwala","Kabirwala Road","Kacha Khuh","Khanewal","Lahore Road","Main Bazaar","Mian Channu","Mian Channu Road","Multan Road","People's Colony","Railway Colony","Sarai Sidhu","Stadium Road","Tariqabad"],
            "Khanpur": ["Grain Market","KLP Road","Liaquatpur Road","Main Bazaar","Model Town","Rahim Yar Khan Road"],
            "Kharian": ["Cantt Area","Dinga Road","G.T. Road","Kotla Arab Ali Khan Road","Main Bazaar"],
            "Khushab": ["Block 1 to 8 (Jauharabad)","Civil Lines (Jauharabad)","Hadali Road","Jauharabad City","Khushab","Main Bazaar Khushab","Muzaffargarh Road","Naushera","Naushera (Wadi Soon)","Noorpur Thal Road","Nurpur","Quaidabad","Railway Road","Satellite Town (Jauharabad)"],
            "Khuzdar": ["Baghbana","Cantt Area","Engineering University Area","Jhalawan Complex","Main Bazaar","RCD Highway"],
            "Kohat": ["Babri Banda","Bannu Road","Basti Fateh Khan","Bazaar Pukhta","Cantt (Kohat Cantonment)","Chashma Road","City Center","Civil Lines","Company Bagh Area","Dhoda Road","G.T. Road","Gari Mawaz Khan","Gulshan Colony","Hangu Road","Industrial Estate","Jangle Khel","Jannat Town","KDA Kohat (Phase 1, 2)","KDA Sector 1 to 9","KDA Town","Kharmatu","Lachi Road","Main Bazaar","Mian Khel","Mir Bash Khel","Model Town","Muhammad Zai","Nawa Kali","Officers Colony","Peshawar Road","Pir Khel","Piro Khel","Pukhta Bazaar","Railway Colony","Rawalpindi Road","Saddar Bazaar Cantt","Shahpur","Shakardarra Road","Tanda Dam Road"],
            "Kot Addu": ["Daira Din Panah Road","KAPCO Colony","Kutchery Road","Layyah Road","Main Bazaar","Muzaffargarh Road","Railway Colony","Sinawan"],
            "Kot Diji": ["Khairpur Road","Kot Diji Fort Area","Main Bazaar","Ranipur Road","Sorah Road"],
            "Kotli": ["Gulhar Sharif","Housing Scheme","Khuiratta Road","Main Bazaar","Mirpur Road","Mohalla Shaheedan","Rawalpindi Road"],
            "Kotli Loharan": ["Main Bazaar","Marakiwal Road","Sialkot Road"],
            "Kot Momin": ["Bhalwal Road","Main Bazaar","Motorway Interchange M-2","Salam Road","Sargodha Road"],
            "Kotri": ["Jamshoro Road","Main Bazaar","National Highway","Railway Junction Area","SITE Area Kotri"],
            "Kot Samaba": ["Main Bazaar","Mandi Bazaar","Rahim Yar Khan Road","Sadiqabad Road","Thatha Road"],
            "Lahore": ["Abbot Road","Abid Market","Agrics - Block A","Agrics - Block B","Agrics - Block C","Ahmad Nagar","Airline Housing Society","Airport Road","Aitchison Society","Ajmeer Town - Block A","Ajmeer Town - Block B","Ajmeer Town - Block C","Ajmeer Town - Block D","Ajmeer Town - Block E","Ajmeer Town - Block F","Ajmeer Town - Block G","Ajmeer Town - Block H","Ajmeer Town - Block I","Ajmeer Town - Block J","Al-Faisal Town","Al-Hamra Town","Al-Jalil Garden","Al-Madina Colony","Al-Noor Town","Al-Rehman Garden","Al-Rehman Garden (Phase 1 to 7)","Ali Park Bedian Road","Ali Town","Ali View Garden","Allama Iqbal Medical Colony","Allama Iqbal Town","Allama Iqbal Town (Asif, Chenab, Huma, Khyber, Mehran, Neelam, Nizam, Pak, Ravi, Raza, Sikandar, Umar, Zeenat Block)","Ameen Park","Amer Sidhu","Amin Park","Anarkali Bazaar","Anarkali Urdu Bazar","Androon Shehr (Walled City)","Architects Housing Society","Arya Nagar","Asif Town","Askari 1","Askari 2","Askari 3","Askari 4","Askari 5","Askari 8","Askari 9","Askari 10","Askari 11","Awaisia Colony","Awan Town","Azam Cloth Market","Aziz Bhatti Town","Badami Bagh","Baghbanpura","Baghriyan","Bahar Colony","Bahria Education & Medical City","Bahria Nasheman","Bahria Orchard (Phase 1, 2, 3, 4)","Bahria Town (Sector A, B, C, D, E, F)","Bahria Town - Awais Qarni Block","Bahria Town - Baba Block","Bahria Town - Ghori Block","Bahria Town - Gulbahar Block","Bahria Town - Haris Block","Bahria Town - Iqbal Block","Bahria Town - Jasmine Block","Bahria Town - Jeneper Block","Bahria Town - Maidos Block","Bahria Town - Nargis Block","Bahria Town - Nashter Block","Bahria Town - Overseas A","Bahria Town - Overseas AA","Bahria Town - Overseas BB","Bahria Town - Overseas Extension","Bahria Town - Quaid-e-Azam Block","Bahria Town - Safari Block","Bahria Town - Shaheen Block","Bahria Town - Spring Block","Bahria Town - Takbeer Block","Bahria Town - Tulip Block","Bahria Town - Umer Block","Barkat Market","Barki Road","Bata Pur","Beadon Road","Bedian Road","Begum Kot","Bhati Gate","Bhatta Chowk","Bhoptian Chowk","Bhubtian Chowk","Bilal Gunj","BOR Society","BOR Society - Block A","BOR Society - Block B","BOR Society - Block C","Bostan Colony","Brandreth Road","Bund Road","Canal Bank Scheme","Canal Berg","Canal City - Block A","Canal City - Block B","Canal City - Block C","Canal City - Block D","Canal Garden","Canal Garden - Block A","Canal Garden - Block AA","Canal Garden - Block B","Canal Garden - Block C","Canal Garden - Block D","Canal Garden - Block E","Canal Garden - Block F","Canal Garden - Block G","Canal Garden - Block H","Canal Road","Canal View Housing Scheme","Cantt","Cantt (Lahore Cantonment)","Cantt View","Cavalry Extension","Cavalry Ground","Central Park Housing Scheme","Chanar Bagh","Chauburji","Chauburji Park","Chinar Bagh","Chowk Yateem Khana","Chung","Chung Adda","Chungi Amar Sidhu","Chung Police Training Center","Circular Road","Civil Defence Colony","Civil Lines","CMH Area","College Road","Darogha Wala","Daroghawala","Data Darbar Area","Davis Road","Defence Road","Defense Road","Dehli Gate","Delhi Gate","Dev Samaj Road","DHA","DHA - Phase 1","DHA - Phase 2","DHA - Phase 3","DHA - Phase 4","DHA - Phase 5","DHA - Phase 6","DHA - Phase 7","DHA - Phase 8","DHA 11 Rahbar (Phase 1, 2)","DHA EME Sector","DHA Phase 1","DHA Phase 2","DHA Phase 3","DHA Phase 4","DHA Phase 5","DHA Phase 6","DHA Phase 7","DHA Phase 8 (Ex Air Avenue \/ Park View)","DHA Phase 9 Prism","DHA Phase 9 Town","Dharampura","Doctors Society","Eden - Canal Villas","Eden - Lane Villas 1","Eden - Lane Villas 2","Eden - Palace Villas","Eden Avenue","Eden Boulevard","Eden City","Eden Gardens","Eden Park","Eden Value Home - Block A","Eden Value Home - Block B","Eden Value Home - Block C","Eden Value Home - Block D","Eden Value Homes","Egerton Road","Elite Town","EME - Block A","EME - Block B","EME - Block C","EME - Block D","EME - Block E","EME - Block F","EME - Block G","EME - Block H","EME - Block J","Empress Road","Engineers Town (Phase 1, 2)","Engineers Town - Phase 3 Block A","Engineers Town - Phase 3 Block A 1","Engineers Town - Phase 3 Block A 2","Engineers Town - Phase 3 Block A 3","Engineers Town - Phase 3 Block B","Engineers Town - Phase 3 Block B 1","Engineers Town - Phase 3 Block B 2","Engineers Town - Phase 3 Block B 3","Engineers Town - Phase 3 Block C","Engineers Town - Phase 3 Block C 1","Engineers Town - Phase 3 Block C 2","Engineers Town - Phase 3 Block C 3","Engineers Town - Phase 3 Block D","Engineers Town - Phase 3 Block D 1","Engineers Town - Phase 3 Block D 2","Engineers Town - Phase 3 Block E","Engineers Town - Phase 3 Block E 1","Engineers Town - Phase 3 Block E 2","Engineers Town - Phase 3 Block E 3","Engineers Town - Phase 3 Block E 4","Engineers Town - Phase 3 Block E 5","Etihad Town (Phase 1, 2)","Faisal Town","Faisal Town (Block A, B, C)","Faisal Town - Block A","Faisal Town - Block B","Faisal Town - Block C","Faisal Town - Block C 1","Faisal Town - Block D","Faisal Town - H.B.F.C Society","Falcon Complex","Fateh Garh","Fazaia Housing Scheme (Phase 1, 2)","Ferozepur Road","Ferozpur Road - Anum Road","Ferozpur Road - Asif Town","Ferozpur Road - Attari Industial Area","Ferozpur Road - Dhulu Khurd","Ferozpur Road - Gajumata","Ferozpur Road - General Hospital","Ferozpur Road - Green Caps","Ferozpur Road - Nishtar Colony","Ferozpur Road - Qainchi","Ferozpur Road - Rohi Nala Industrial","Ferozpur Road - Saroba Gardens","Ferozpur Road - Sattelite Town","Ferozpur Road - Shadab Colony","Fortress Stadium Area","Gajjumatta","Garden Town","Garden Town (Babur, Tariq, Usman, Aibak, Ali, Abu Bakar Block)","Garhi Shahu","Gawalmandi","Gawal Mandi","Ghaziabad","Ghazi Road","Ghosia Colony","Gor-1","Gor-2","Gor-3","Green Cap Housing Society","Green Park","Green Town (Sector 1, 2, 3, 4, 5)","Green Town - Sector D 1 Block 1","Green Town - Sector D 1 Block 2","Green Town - Sector D 1 Block 3","Green Town - Sector D 1 Block 4","Green Town - Sector D 1 Block 5","Green Town - Sector D 2 Block 1","Green Town - Sector D 2 Block 2","Green Town - Sector D 2 Block 3","Green Town - Sector D 2 Block 3 Nill","Green Town - Sector D 2 Block 4","Green Town - Sector D 2 Block 5","Green Town - Sector D 2 Block 5 Nill","GT Road (Lahore bypass)","Gujjar Pura","Gul-e-Daman - Block A","Gul-e-Daman - Block B","Gulbahar Colony","Gulberg","Gulberg 1","Gulberg 2","Gulberg 2 Block A","Gulberg 2 Block B","Gulberg 2 Block C","Gulberg 2 Block D","Gulberg 2 Block E","Gulberg 2 Block F","Gulberg 2 Block G","Gulberg 2 Block H","Gulberg 2 Block J","Gulberg 2 Block K","Gulberg 2 Block L","Gulberg 2 Block M","Gulberg 2 Block N","Gulberg 2 Block P","Gulberg 2 Block Q","Gulberg 2 Block R","Gulberg 2 Block S","Gulberg 2 Block T","Gulberg 3","Gulberg 3 Block A 1","Gulberg 3 Block A 2","Gulberg 3 Block A 3","Gulberg 3 Block B 1","Gulberg 3 Block B 2","Gulberg 3 Block B 3","Gulberg 3 Block C 1","Gulberg 3 Block C 2","Gulberg 3 Block C 3","Gulberg 3 Block D 1","Gulberg 3 Block D 2","Gulberg 3 Block D 3","Gulberg 3 Block E 1","Gulberg 3 Block E 2","Gulberg 3 Block E 3","Gulberg 3 Block G","Gulberg 3 Block H","Gulberg 3 Block J","Gulberg 3 Block K","Gulberg 3 Block L","Gulberg 3 Block M","Gulberg 3 Block N","Gulberg 3 Block P","Gulberg 3 Falcon Complex","Gulberg 3 Firdous Market","Gulberg 4","Gulberg 4 FCC","Gulberg 4 FCC Kachi Basti","Gulberg 4 Old FCC","Gulberg 5","Gulberg 5 Aziz Avenue","Gulberg 5 Bridge Colony","Gulberg 5 Bridge Line","Gulberg 5 Ghos-e-Azam Colony","Gulberg 5 Zafar Ali Road","Gulberg Guru Mangat Road","Guldasht Town","Gulshan-e-Lahore","Gulshan-e-Mustafa","Gulshan-e-Ravi (Block A to G)","Gulshan-e-Ravi - Gulberg","Gulshan-e-Ravi - Shalamar Town Rustam Park","Gulshan-e-Shalimar","Gulshan Ravi","Guru Mangat","Hall Road","Haloki","Handu Gujjar","Harbanspura","Ichhra","Ideal Garden Housing Scheme","Iqbal Avenue - Phase 1","Iqbal Avenue - Phase 2","Iqbal Avenue - Phase 2 Block B","Iqbal Avenue - Phase 3","Iqbal Avenue - Phase 3 Block A","Iqbal Avenue - Phase 3 Block B","Iqbal Avenue - Phase 3 Block C","Iqbal Avenue - Phase 3 Block D","Iqbal Park","Iqbal Town - Asif Block","Iqbal Town - Badar Block","Iqbal Town - College Block","Iqbal Town - Gulshan Block","Iqbal Town - Huma Block","Iqbal Town - Hunza Block","Iqbal Town - Jahanzaib Block","Iqbal Town - Kamran Block","Iqbal Town - Kareem Block","Iqbal Town - Kashmir Block","Iqbal Town - Mehran Block","Iqbal Town - Nargis Block","Iqbal Town - Neelam Block","Iqbal Town - Neezam Block","Iqbal Town - Nishter Block","Iqbal Town - Pak Block","Iqbal Town - Rachna Block","Iqbal Town - Ravi Block","Iqbal Town - Raza Block","Iqbal Town - Satluj Iqbal","Iqbal Town - Sikandar Block","Iqbal Town - Umer Block","Islamia Park","Islam Park","Islampura (Krishan Nagar)","Izmir Town","Jaffar Town","Jail Road","Jallo","Jallo Park Area","Jamil Town","Jinnah Hospital Area","Johar Town","Johar Town (Phase 1 Block A to H)","Johar Town (Phase 2 Block J to R)","Johar Town - Block A","Johar Town - Block A 1","Johar Town - Block A 2","Johar Town - Block A 3","Johar Town - Block B","Johar Town - Block B 1","Johar Town - Block B 2","Johar Town - Block B 3","Johar Town - Block C","Johar Town - Block C 1","Johar Town - Block C 2","Johar Town - Block D","Johar Town - Block D 1","Johar Town - Block D 2","Johar Town - Block E","Johar Town - Block E 1","Johar Town - Block E 2","Johar Town - Block F","Johar Town - Block F 1","Johar Town - Block F 2","Johar Town - Block G","Johar Town - Block G 1","Johar Town - Block G 3","Johar Town - Block G 4","Johar Town - Block H","Johar Town - Block M","Jubilee Town","Judicial Colony - Phase 1","Judicial Colony - Phase 2","Kacha Jail Road","Kahna","Kahna Nau","Kala Khatai Road","Kallar Syedan Road","Kalma Chowk Area","Kamahan","Karim Park Ravi Road","Kashmir Road","Kasoor Road - Center Park","Kasoor Road - Kahna Nau","Kasoor Road - Kahna Old","KB Colony","Khan Colony","Khayaban-e-Jinnah Road - Audit Accounts Society","Khayaban-e-Jinnah Road - AWT Phase 2","Khayaban-e-Jinnah Road - Bahria Orchard","Khayaban-e-Jinnah Road - Gulshan e Lahore","Khayaban-e-Jinnah Road - Khayban-e-Amin","Khayaban-e-Jinnah Road - Lake City","Khayaban-e-Jinnah Road - Shareef Medical Jati Umra","Khayaban-e-Jinnah Road - Tariq Gardens","Khayaban-e-Jinnah Road - UET Society","Khayaban-e-Jinnah Road - Valencia Town","Khuda Buksh Colony KB Society","Khyaban-e-Amin (Block A to R)","Khyaban-e-Jinnah","Kosar Colony","Kot Abdul Malik","Kot Khawaja Saeed","Kot Lakhpat","Kot Lakh Pat - Dilkhushan","Kot Lakh Pat - Kot Lakh Pat Indutrial Area","Kot Lakh Pat - Kot Lakh Pat Staion Area","Kot Lakh Pat - Liaqatabad","Kot Lakh Pat - Modern Colony","Kotli Peer Abdul Rehman","Krishan Nagar","Lake City (Sector M1 to M8)","Lakshmi Chowk","Lalazar Housing Scheme","Lal Pul","Lawrence Road","LDA Avenue 1 (Block A to M)","LDA City","Liberty Market Area","Link Road Model Town","Loha Market","Loharan Wala Kho","Lohari Gate","Lower Mall","Lower Mall Road","Madina Town","Main Boulevard Gulberg","Main Gullberg","Main Market Gulberg","Mall Road","Mall Road (The Mall)","Manawan","Manga Mandi","Mansoorah","Maraca","Marghazar Colony","Mateen Avenue","Mayo Hospital","Mayo Hospital Area","McLeod Road","Mehar Fayyaz Colony","Mehmood Buti Interchange","Mian Mir Colony","Mian Plaza","Mianwali Colony","Military Accounts - Block A","Military Accounts - Block B","Military Accounts - Block C","Military Accounts - Block D","Military Accounts - Block E","Military Accounts Society","Mini Market","Misri Shah","M M Alam Road","Model Colony","Model Town","Model Town (Block A to M)","Model Town - Block A","Model Town - Block B","Model Town - Block C","Model Town - Block D","Model Town - Block E","Model Town - Block F","Model Town - Block G","Model Town - Block H","Model Town - Block J","Model Town - Block K","Model Town - Block L","Model Town - Block M","Model Town - Block N","Model Town - Block P","Model Town - Block Q","Model Town - Block R","Model Town - Block S","Model Town - Square Market","Model Town Extension","Mohlanwal","Mohni Road","Mozang","Mughalpura","Muhafiz Town - Block A","Muhafiz Town - Block B","Muhafiz Town - Block C","Muhafiz Town - Block D","Muhafiz Town - Block E","Multan Road","Muslim Town","Muslim Town (Old \/ New)","Mustafa Town","Nasheman-e-Iqbal (Phase 1, 2)","National Town","Naval Anchorage Lahore","Nawab Town","Nazir Garden Society","Naz Town","Nespak - Phase 1 Block D 1","Nespak - Phase 1 Block D 2","Nespak - Phase 1 Block E","Nespak - Phase 2 Block A","Nespak - Phase 2 Block B","Nespak - Phase 2 Block C","Nespak - Phase 2 Block D","Nespak - Phase 3 Block A","Nespak - Phase 3 Block B","Nespak Housing Scheme (Phase 1, 2)","New Iqbal Park","New Super Town","Nisbat Road","Nishat Colony","Nishtar Colony","Nishtar Town","Nizamabad","Nobel Town","Nonarian","NTC Housing Scheme","Officers Colony Cantt","Old Anarkali","Oqaf Colony","Pak Arab Housing Society (Phase 1, 2)","Pak Arab Society - Block A","Pak Arab Society - Block B","Pak Arab Society - Block BB","Pak Arab Society - Block C","Pak Arab Society - Block D","Pak Arab Society - Block E","Pak Arab Society - Block F","Pak Arab Society - Pak Arab Society Vittal Homes","Pakki Thatti","Pantheist Colony","Paragon City (Imperial, Orchard, Woods, Barki Garden)","Park Avenue Housing Scheme","Park View City (Tulip, Jasmine, Rose, Topaz, Jade, Pearl Block)","Passco Housing Society","Patiala Ground","PCSIR Housing Society (Phase 1, 2)","Peoples Colony","PGSHS","Phool Nagar Area","PIA Housing Society (Phase 1, 2)","PIA Society - Block A","PIA Society - Block A 1","PIA Society - Block B","PIA Society - Block B 1","PIA Society - Block C","PIA Society - Block D","PIA Society - Block E","PIA Society - Block F","PIDC Housing Society","Pine Avenue","Poonch Road","PRC Housing Society","Prem Nagar","Punjab Coop Housing Society (Block 1 to 5)","Punjab Govt Employees Housing Scheme","Punjab Small Industries Housing Scheme","Punjab Society - Block A","Punjab Society - Block B","Punjab Society - Block C","Punjab Society - Block D","Punjab Society - Phase 1 Block A 1","Punjab Society - Phase 1 Block A 2","Punjab Society - Phase 1 Block A 3","Punjab Society - Phase 1 Block A 4","Punjab Society - Phase 1 Block A 5","Punjab Society - Phase 1 Block B 1","Punjab Society - Phase 1 Block B 2","Punjab Society - Phase 1 Block B 3","Punjab Society - Phase 1 Block C 2","Punjab Society - Phase 1 Block R","Punjab Society - Phase 2 Block A","Punjab Society - Phase 2 Block B","Punjab Society - Phase 2 Block C","Punjab Society - Phase 2 Block D","Punjab Society - Phase 2 Block E","Punjab University Employees Housing Scheme (Town Campus)","Punjab University New Campus","Qaddafi Stadium Area","Qaid-e-Azam Industrial Estate","Qartaba Chowk Area","Qasim Pura","Qasori Road","Qila Gujar Singh","Qila Lachman Singh","R.A Bazar","Rail Town","Railway Colony (Carriage Shop)","Railway Headquarters Area","Railway Station","Railway Station Area","Raiwand Road - Airline Society","Raiwand Road - Ali Raza Abad Rawind Road","Raiwand Road - Ali Town","Raiwand Road - Architect Society","Raiwand Road - Doctors Society","Raiwand Road - Dream Garden","Raiwand Road - Dubai Town","Raiwand Road - OPF Society","Raiwand Road - Shair Shah Colony","Raiwind City","Raiwind Road","Rajpoot Town - Block A","Rajpoot Town - Block B","Rajpoot Town - Block C","Rajpoot Town - Block D","Rana Town","Rang Mahal","Ravi Clifton","Ravi Road","Rehman Garden Phase 1","Rehman Garden Phase 2","Rehman Gardens","Rehmanpura","Resham Gali","Revenue Employees Cooperative Housing Society (RECHS)","Revenue Society","Rewaz Garden","River View Society - Block A","River View Society - Block B","Riwaz Garden","Royal Residencia","Sabzazar","Sabzazar (Block A to P)","Sabzazar - Mansoorah","Sadar Cantt","Saddar Bazaar Cantt","Saeed Park","Saggiyan Bypass","Sahowala","Salamatpura","Samanabad","Sanat Nagar","Sanda","Sanda (Khurd, Kalan)","Sanda Islampura - Chaburji","Sanda Islampura - Dev Samajh Road","Sanda Islampura - Govt.College University","Sanda Islampura - Islampura","Sanda Islampura - Outfall Road","Sanda Islampura - Rajghar","Sanda Islampura - Rewarz Garden","Sanda Islampura - Rizwan Gardens","Sanda Islampura - Sanat Nagar","Sanda Islampura - Sanda","Sanda Islampura - Shamnagar","Sant Nagar","Sarba","Sarfraz Rafiqui Road","Sarwar Colony Lahore Cantt","Sarwar Road Lahore Cantt","Sarwar Town","Satellite Town Lahore","Scarsdale National Colony","Sehjpal","Shabbir Town","Shadab Colony","Shadbagh","Shad Bagh","Shadmaan","Shadman 1","Shadman 2","Shah Alam","Shahbagh China Scheme - Sadiqia Colony","Shahbagh China Scheme - Salamat Pura","Shahbagh China Scheme - Walled City Lahore","Shahdara Town","Shah Di Khoi","Shahdra - Begamkot Sharkpur Road","Shahdra - Imamia Colony Rachna Town","Shahdra - Shahdra Town","Shah Faisal Town","Shah Jamal","Shahkam Chowk","Shahpur Kanjra","Shah Pur Kanjra","Shahpur Kanjran","Shalamar Town","Shalimar Link Road","Shalimar Town","Shama Chowk Area","Shami Road Lahore Cantt","Sham Nagar","Shaukat Khanum Hospital Area","Shera Kot","Sheraz Town","Sher Shah Colony","Shimla Pahari","Singhpura","Sitara Colony - Colony 1","Sitara Colony - Colony 2","St. Francis Colony","Staff Colony - Block A","Staff Colony - Block B","Staff Colony - Block C","Staff Colony - Block D","Staff Colony - Block E","Staff Colony - Phase 1","Staff Colony - Phase 2 Block A","Staff Colony - Phase 2 Block B","Staff Colony - Phase 2 Block C","Staff Colony - Phase 2 Block D","Staff Colony - Phase 3 Block A","Staff Colony - Phase 3 Block B","Staff Colony - Phase 3 Block C","State Life Cooperative Housing Society (Phase 1, 2)","State Life Society","Sui Gas Housing Society (Phase 1, 2)","Sui Gas Society","Sukh Chain Garden - Block A","Sukh Chain Garden - Block B","Sukh Chain Garden - Block C","Sukh Chain Garden - Block D","Sukh Chain Garden - Block E","Sukh Chain Garden - Block F","Sukh Chain Garden - Block G","Sukh Chayn Gardens","Sultan Pura","Sunfort Gardens","Super Town","Taj Bagh Scheme","Tajpura","Tajpura Scheme","Tariq Garden","Tech Society","Tezab Ahata","Thokar Canal Road - DHA Rehber Phase 11","Thokar Canal Road - New Shaheen Block Bahria","Thokar Canal Road - P&D Apara Housing Society","Thokar Canal Road - Sultan Town","Thokar Canal Road - Westwood Colony","Thokar Niaz Baig","Timber Market","Tipu Block Garden Town","Town Ship","Township (Sector A-1, A-2, B-1, B-2, C-1, C-2, D-1, D-2)","Town Ship - Sector A 1","Town Ship - Sector A 2","Town Ship - Sector B 1","Town Ship - Sector B 2","Town Ship - Sector C 1","Town Ship - Sector C 2","Tricon Village","Triocon Village - Block A","Triocon Village - Block B","Triocon Village - Block C","Triocon Village - Block D","Trust Colony","Ulfat Town","Upper Mall","Urdu Bazaar Lahore","Valencia Town (Block A to P)","Vehari Colony","Venus Housing Scheme","Wahdat Colony","Wahdat Road","Walled City (Delhi, Lohari, Bhati, Kashmiri, Sheranwala Gate)","Walton Road","Walton Road (Cantt)","Wapda Town","Wapda Town (Phase 1 Block A to K)","Wapda Town (Phase 2 Block M to Q)","Wapda Town - Phase 1","Wapda Town - Phase 2","Waris Road","West Wood Colony","Yateem Khana","Yousaf Colony","Zafar Colony","Zaman Park","Zarar Shaheed Road - Gulistan Colony","Zarar Shaheed Road - P.A.F New Officer Coloney","Zarar Shaheed Road - P.A.F Old Officer Coloney","Zero Point Lahore"],
            "Lalamusa": ["G.T. Road","Jaura Road","Kunjah Road","Main Bazaar","Railway Road"],
            "Larkana": ["Ahsan Housing Colony","Ali Goharabad","Allahabad","Al Madina Colony","Baharpur Mohalla","Bakrani Road","Bank Square","Baqapur","Bheda Goth","Chandka Medical College Area","Dari Mohalla","Ghabi Khan Chandio Mohalla","Goth Aitbar Dahani","Goth Dolat Khuhro","Goth Ghulab Bhutto","Goth Gullan Korai","Goth Kotai Bhutto","Goth Udha","Goth Wada Phul","Kanga","Karma Bagh","Khaliq Colony","Khosa","Lahori Mohalla","Mahar Wada","Main Bazaar","Mirokhan Road","Noor Housing Colony","Pakistan Chowk","Rahmatpur Mohalla","Resham Gali","Sachal Colony","Seerai Bazu","Shahbaz Colony","Shaikh Zaid Hospital Area","Site Area","Station Road","VIP Road","Waleed","Waleed Mohalla","Zamaan Goth"],
            "Layyah": ["Chaubara","Chaubara Road","Chowk Azam","College Road","Fatehpur","Fatehpur Road","Housing Colony","Karor Lal Esan","Karor Lal Esan Road","Kot Sultan","Kot Sultan Road","Layyah","Main Bazaar","Mandi Town","Multan Road","Railway Road","Ward 1 to 10"],
            "Liaquatpur": ["Allahabad Road","Feroza Road","Grain Market","Khanpur Road","Kutchery Road","Main Bazaar","Model Town","Railway Road"],
            "Lodhran": ["Bahawalpur Road","Dunyapur","Dunyapur Road","Kahror Pakka","Kehrur Pacca Road","Lodhran","Main Bazaar","Multan Road","Officers Colony","Super Town"],
            "Loralai": ["Bacha Khan Chowk","Cantt Area","D.G. Khan Road","Main Bazaar","Quetta Road","Zhob Road"],
            "Mailsi": ["Colony Road","Dunyapur Road","Grain Market","Main Bazaar","Mitroo Road","Railway Road","Vehari Road"],
            "Malakwal": ["Bhalwal Road","Main Bazaar","Mandi Bahauddin Road","Pind Dadan Khan Road","Railway Junction Area"],
            "Mandi Bahauddin": ["Canal View","Chillianwala Road","College Road","Gujrat Road","Kutchery Road","Main Bazaar","Malakwal Road","Model Town","Mong","Phalia Road","Railway Road","Shaheedanwali","Sohawa","Wasu"],
            "Manga Mandi": ["Bypass Area","Main Bazaar","Multan Road","Raiwind Road","Sundar Road"],
            "Mansehra": ["Abbottabad Road","Baffa Road","Balakot Road","College Doraha","Ghazikot Township","Karakoram Highway (KKH)","Khaki Road","Main Bazaar","Oghi Road","Shinkiari Road"],
            "Mardan": ["Bagh-e-Iram","Baghdad Colony","Bakshali Road","Bank Road","Bicket Gunj","Canal Road","Cantt (Mardan Cantonment)","Charsadda Road","Chumtarr","College Road","Dharmsala","Dost Muhammad Colony","Fatima Jinnah Colony","Gaju Khan Chowk Area","General Bus Stand Area","Gujjar Garhi","Hoti","Industrial Estate","Jalala Road","Katlang Road","Khadagzai","Liaquat Park Area","Malakand Road","Mayar","Muqam Road","Nowshera Road","Par Hoti","Par Hoti Bazaar","Rashakai Road","Rifaqat Colony","Rustam Road","Saddar Bazaar Cantt","Sardar Garhi","Sarfaraz Gunj","Sector A to E (Sheikh Maltoon Town)","Shamsi Road","Sheikh Maltoon Town","Sugar Mills Area","Swabi Road","Takht Bhai Road","Taru Jabba Road"],
            "Matli": ["Badin Road","Hyderabad Road","Main Bazaar","Sugar Mills Area","Tando Ghulam Ali Road"],
            "Mehar": ["Dadu Road","Indus Highway","Main Bazaar","Nasirabad Road","Radhan Road"],
            "Mian Channu": ["Abdul Hakim Road","G.T. Road","Grain Market","Main Bazaar","Model Town","Railway Station Area","Talamba Road"],
            "Mianwali": ["Ballo Khel Road","Chashma Barrage Colony","City Center","Gulberg Colony","Isakhel","Isakhel Road","Kundian Road","Kutchery Road","Liaquatabad","Main Bazaar","Mianwali","PAF Colony","Piplan","Piplan Road","Sargodha Road","Watta Khel","Yaroo Khel"],
            "Minchinabad": ["Bahawalnagar Road","Main Bazaar","Mandi Sadiq Ganj Road","Pakpattan Canal Road"],
            "Mirpur": ["Allama Iqbal Road","Bank Hurmat","Bradford Chowk Area","Cantt Area","Chaksawari Road","Chechian","Chowk Shaheedan","Degree College Area","DHA Mirpur","F\/1","F\/2","F\/3","F\/4","G\/1","G\/2","G\/3","G\/4","Industrial Estate","Islamgarh Road","Jari Kas","Kalyal","Khadan","Kotli Road","Main Bazaar","Mangla Road","Mian Muhammad Town","Model Town","Naangi","New City (Sector B1 to B4)","New Mirpur City","Officers Colony","Paniola Road","Pullo Manda","Quaid-e-Azam Stadium Area","Sector A1 to A4","Sector B1 to B4","Sector C1 to C4","Sector D1 to D4","Stadium Road","Thothal"],
            "Mirpur Khas": ["Gulshan-e-Iqbal","Hirabad","Hyderabad Road","Jarwar","Khipro Road","Lalchandabad","Main Bazaar","Mirpur Khas","Ring Road","Satellite Town","Sir Syed Road","Station Road","Umer Kot","Umerkot Road"],
            "Mithi": ["Diplo Road","Islamkot Road","Main Bazaar","Naukot Road","Thar Coal Road"],
            "Moro": ["Dadu Road (Amri Bridge)","Main Bazaar","National Highway","Naushahro Feroze Road"],
            "Multan": ["Abdali Road","Al-Quraish Housing Scheme","Ali Town","Al Jilan Town","Al Qureshi Housing Scheme","Altaf Town","Ansari Colony","Askari Colony (1, 2)","Bagh Lange Khan","Bahadurpur","Basti Kayan Pur","Bawa Safra","Bomanji Square","Bosan Road","Buch Executive Villas","Cantonment","Cantt (Multan Cantonment)","Chah Boharwala","Chowk Kumharanwala","Chowk Shaheedan","Chungi No. 1","Chungi No. 6","Chungi No. 7","Chungi No. 8","Chungi No. 9","Chungi No. 14","Circular Road","Citi Housing Multan","Civil Lines","Daulat Gate","Defence Officer Colony","Delhi Gate","Dera Adda","DHA Multan (Sector A to Y)","Double Phatak","Faizabad","Faiz Town","Fatima Jinnah Town","Garden Town","General Bus Stand Area","Ghanta Ghar Area","Ghaziabad","Gulberg Colony","Gulgasht Colony","Gulraiz Town","Gulshan-e-Mehar","Gulshan-e-Qadir","Gulshan Market","Haram Gate","Harmain City","Hassan Parwana Colony","Hussain Agahi Bazaar","Industrial Estate","Iqra Town","Ismail Abad","Jahangirabad","Jalalabad","Jalilabad","Jamilabad","Jamilabad Housing Scheme","Jhakarpur","Jinnah Town","Johar Town","Kalma Chowk","Karam Khanwala","Katchery Road","Khanewal Road","Kotla Tolay Khan","Lohari Gate","Lutfabad","Madina Colony","Maitla Town","Mall Road Cantt","Masoom Shah Road","Mattital Road","MDA Chowk","MDA Officers Housing Society","Model Town (A, B, C)","Model Town A","Moza Nigana Durana","Mujahid Town","Multan","Mumtazabad","Muzafarabad","Nagas Shah","Naseerabad","Naval Base Area","New Multan (Block A to Z)","New Multan - Block V","New Shalimar Colony","Nishtar Road","Northern Bypass","Officers Colony","Old Shujabad Road","Pak Gate","Peer Khurshid Colony","Peoples Colony","Qaddafi Chowk Area","Qasim Bela","Qasim Pur Colony","Railway Colony","Raza Abad","Rehmat Town","Royal Orchard","Saleemabad","Sameeja Abad","Satellite Town","Shah Gardez","Shah Rukn-e-Alam Colony (Phase 1, 2)","Shah Shams Colony","Shalimar Colony","Shamasabad Colony","Shershah Road","SherShah Town","Sooraj Miani","Suraj Kund Road","Tariq Road","Timber Market","Vehari Chowk","Vehari Road","Wapda Town","Wapda Town (Phase 1, 2, 3)","Waqas Town","Yasrab Madina Town","Zakariya Town"],
            "Murree": ["Ayubia Road","Bhurban","Changla Gali","Ghoragali","Jhika Gali","Kashmir Point","Kuldana","Lawrence College Area","Lower Topa","Pindi Point","Sunny Bank","The Mall (Mall Road)","Upper Topa"],
            "Muzaffarabad": ["Airport Area","Al-Makki Colony","Ambore","Bait-ul-Maal Colony","Bank Road","Basnara","Bilal Colony","Chattar Domel","Chehla Bandi","CMH Area","Domel (Sangam)","Eidgah Road","Ghari Pan","Gojra (Bypass)","Kallar Syedan","Kamsar","Lower Chattar","Madina Market","Main Bazaar","Makri","Mirwaiz Colony","Naluchi","Neelam Valley Road","Plate Area","Police Line Area","Rahimkot","Rathoi","Rawalpindi Road","Saddar Bazaar","Saheli Sarkar Area","Sarai Bandi","Secretariat Area","Shoukat Lines","Subri","Tariqabad","Upper Chattar","Upper Plate","Wapda Colony"],
            "Muzaffargarh": ["Alipur","Alipur Road","Basti Malook Area","Canal Colony","City Center","Fazal Cloth Mills Area","Jatoi","Jatoi Road","Kot Addu","Kot Addu Road","Kutchery Road","Main Bazaar","Multan Road","Muzaffargarh","Railway Colony","Thermal Power Station Colony"],
            "Nankana Sahib": ["Bucheki Road","Grain Market","Gurdwara Janam Asthan Area","Housing Colony","Main Bazaar","Mandi Faizabad Road","Model Town","Railway Road","Shahkot Road"],
            "Narowal": ["Baddomalhi Road","Bilal Colony","Circular Road","College Road","Kachehri Road","Main Bazaar","Model Town","Narowal","Pasrur Road","Railway Road","Shakargarh Road","Shakkargarh","Siddique Town","Zafarwal Road"],
            "Naushahro Feroze": ["Bhiria Road","Main Bazaar","Moro Road","National Highway","Tharushah Road"],
            "Nawabshah (Shaheed Benazirabad)": ["Gharibabad","Hospital Road","Kazi Ahmed Road","Line Par","Main Bazaar","Manuabad","Monotechnic Road","Quaid-e-Awam University Area","Sakrand Road","Sanghar Road","Society Area","Taj Colony"],
            "Nowshera": ["Akora Khattak","ASC Colony","G.T. Road","Hakimabad","Jalozai Road","Kalan","Nowshera Cantt","Pabbi","Risalpur Road","Shaidu","Taru Jabba"],
            "Okara": ["Akbar Road","Al-Jannat Colony","Allama Iqbal Town","Ameer Colony","Bari Town","Basti Iqbal Nagar","Basti Rehmatpura","Benazir Colony","Block A","Block B","Block D","Canal View","Cantt (Okara Cantonment)","Chak 4\/4 L","Chak 4\/4L","Chak 53\/2L","Chak 54\/2L","Chak 56\/2 L","Circular Road","Civil Lines","Depalpur","Depalpur Chowk Area","Depalpur Road","Faisalabad Road","Faisal Colony","Faisal Mehmood Colony","Faizabad Colony","Farid Town","General Bus Stand Area","Gole Chowk Area","Green City","Gulshan-e-Iqbal","Gulshan Iqbal Town","Housing Colony (1, 2, 3)","Imran Town","Irshad Town","Javed Colony","Jinnah Colony","Katchery Road","Kot Jan Muhammad","Madina Colony","Main Bazaar","Mandi Road","Mehar Colony","Mehboob Colony","Model Cooperative Housing Society","Model Town","Mujahid Colony","Muslim Town","National Highway (G.T. Road)","Niazi","Noor Garden","Okara","Old City","Peoples Colony","Police Lines Area","Railway Road","Ravi Road","Rehan Kot","Rehman City","Saad City","Sabir Piya Town","Sabri Colony","Saddar Bazaar","Sakri Colony","Samad Pura","Shams Colony","Sher Rubani Town","Stadium Road"],
            "Pakpattan": ["Arifwala Road","Canal Colony","College Road","Dargah Baba Farid Area","Grain Market","Kutchery Road","Main Bazaar","Minchinabad Road","Mohalla Pir Bukhari","Sahiwal Road","Tibbi Lal Beg"],
            "Pano Aqil": ["Cantt Area","Main Bazaar","National Highway","Rohri Road","Sukkur Road"],
            "Pasrur": ["Daska Road","Gujranwala Road","Main Bazaar","Narowal Road","Sialkot Road","Zafarwal Road"],
            "Pattoki": ["Chunian Road","Flower Market Area","Halla Road","Kanganpur Road","Main Bazaar","Model Town","Multan Road","Railway Road"],
            "Peshawar": ["Abdara Road","Academy Town","Ahmadabad","Airport Road","Al-Haram Town","Ali Town","Arbab Road","Ashrafia Colony","Askari 1","Askari 2","Askari 3","Askari 4","Askari 5","Askari 6","Bacha Khan Chowk","Badhaber","Bahadar Kalay","Bara Road","Bhanamari","Board Bazaar","Cantt (Peshawar Cantonment)","Chamkani","Charsadda Road","Chowk Yadgar","Civil Quarters","Dabgari Gardens","Dalazak Road","Danishabad","Deans Trade Centre Area","Defence Colony","DHA Peshawar (Sector A to J)","Doranpur","Faqirabad (1, 2)","Forest Bazar","G.T. Road Peshawar","Garhi Qamar Din","Gora Qabristan Area","Gulbahar (No. 1, 2, 3, 4)","Gulberg","Gulshan-e-Rahman","Gulshan-e-Umar","Haji Camp Area","Hashtnagri","Hayatabad (Phase 1 to 7)","Industrial Estate (Hayatabad)","Islamia College Area","Jehangir Pura","Karimpura","Karkhano Market","Khyber Bazaar","Kohat Road","Kotla Mohsin Khan","Lahori Gate","Landi Arbab","Malik Pura","Mall Road Cantt","Mathra","Michni Road","Momin Town","Nasir Bagh Road","Nauthia (Jadid, Qadeem)","New Rampura","Nishtarabad","Old City (Androon Shehr)","Pajagi Road","Palosi","Palosi Road","Peshawar University Campus","Phandu Road","Pishtakhara","Qissa Khwani Bazaar","Rahatabad","Railway Road","Ramdas","Regi Model Town (Zone 1 to 5)","Ring Road (Northern \/ Southern)","Saddar Road Cantt","Saeedabad","Shaheen Town","Shahi Bagh Area","Shahpur","Shami Road Cantt","Sheikh Muhammadi","Sikandar Town","Sufaid Dheri","Sunehri Masjid Road","Tahkal Bala","Tahkal Payan","Tehkal","Tilakpura","University Road","University Town","Wapda Town","Warsak Road","Wazir Bagh Area","Yakatoot","Yousafabad","Zaryab Colony"],
            "Phalia": ["Busal Road","Gujrat Road","Kunjah Road","Main Bazaar","Mandi Bahauddin Road"],
            "Pindi Bhattian": ["Chiniot Road","Hafizabad Road","Main Bazaar","Motorway Interchange M-2\/M-3","Sukheke Road"],
            "Pindi Gheb": ["Basal Road","Main Bazaar","Rawalpindi Road","Sil River Area","Talagang Road"],
            "Pishin": ["Khanozai Road","Main Bazaar","Quetta Road","Saranan Road","Yaro Road"],
            "Quetta": ["Abdul Sattar Road","Ahmad Khanzai","Ahmedwal","Airport","Airport Road","Al-Gilani Housing","Alamdar Road","Ali Town","Arbab Karam Khan Road","Askari Checkpost Area","Askari Park Area","Baleli","Barah Khana","Barshore","Bostan","Brewery Road","Burma Hotel Area","Cantt (Quetta Cantonment)","Chaman","Chaman Housing Scheme","Chashma Achozai","Chiltan Housing Scheme","Circular Road","Civil Hospital Area","Civil Lines","Customs Colony","Dalbandin","Digari","Double Road","Eastern Bypass","Fatima Jinnah Road","Gawalmandi","Ghafoor Town","Ghosia Colony","Gurdat Singh Road","Hanna Urak","Harnai Bus Stop Area","Hazara Town","Hazar Gangi","Hudda","Hurram Zai","Imdad Chowk Area","Jabal-e-Noor","Jinnah Road","Jinnah Town","Joint Road","Kandahari Bazaar","Kasi Road","Khanozai","Kharan","Killa Abadulllah","Killi Abdur Rehman Zai","Killi Almas","Killi Deba","Killi Gul Muhammad","Killi Ismail","Killi Kamalo","Killi Kotwal","Killi Paind Khan","Killi Shabak","Killi Shabo","Killi Umar","Killi Verchoom","Kohi Taftan","Kuchlak","Kuchlak Road","Ladgasht","Liaquat Bazaar","Link Road","Malguzar Road","Manan Chowk Area","Marriabad","Masjid Road","McConaghey Road","Mission Road","Model Town","Murree Abad","Nawa Killi","New Hanna","Nodhiez","Nokundi","Nushki","Pashtunabad","Patel Bagh","Pishin","Pishin Stop Area","Prince Road","Quarry Road","Quetta","Railway Colony","Raisani Road","Sabzal Road","Sadiq Shaheed Park Area","Samungali","Samungli Road","Samungli Town","Saranan","Sariab","Sariab Custom","Sariab Millat","Sariab Road","Satellite Town","Satellite Town (Block 1 to 5)","Shahbaz Town (Phase 1 to 4)","Shaldara","Shawalkot","Sirki Road","Sorange","Spinny Road","Staff College","Stewart Road","Suraj Gunj Bazaar","Survey 144","Toghi Road","Wahdat Colony","Western Bypass","Yateem Khana Area","Zarghoon Road","Ziarat","Ziarat Stop Area"],
            "Rahim Yar Khan": ["Abasia Bungalows","Abbasia Town","Abu Dhabi Road","Airport Area","Airport Road","Al-Mustafa Town","Baghdad Colony","Basti Amanat Ali","Block Y","Canal Colony","Chak 72\/NP","Chak 110\/P","Circular Road","City Center","Civil Lines","Commercial Market","Dari Sangi","Dastagir Colony","DHA Rahim Yar Khan","Factory Area","Gulberg Town","Gulshan-e-Iqbal","Gulshan-e-Osman","Gulshan-e-Usmani","Habib Colony","Hanifia Colony","Hassan Colony","Industrial Area","Jaddah Town","Jail Road","Jinnah Colony","Kacha Sadiqabad Road","Kachehri Road","Kanjo Muhalla","Khanpur Road","Liaquatpur Road","Madina Colony","Main Bazaar","Model Town","Mubarakpur Road","Muhammadia Colony","Muslim Town","National Highway Road","New Sadiq Colony","Niazi Colony","Officers Colony","Old City","Rahim Yar Khan","Railway Colony","Rehman Colony","Rehmat Nyazi Colony","Saddar Bazaar","Sadiq Colony","Satellite Town","Scheme 2","Shafi Town","Shahbaz Pur Road","Stadium Road","Tariqabad","Trust Colony","Zafar Abad Colony"],
            "Raiwind": ["Kasur Road","Lahore Road","Main Bazaar","Manga Road","Railway Junction Area","Sundar Industrial Estate"],
            "Rawalakot": ["CMH Road","Hajira Road","Housing Scheme","Kahutta Haveli","Main Bazaar","Rawalakot","Singola","Thorar Road","University Campus Area"],
            "Rawalpindi": ["Adiala Road","Adiyala Road","Adyala","Afshan Colony","Afzal Town","Airport Housing Society","Airport Road","Al-Noor Colony","Ali Town","Allama Iqbal Colony","Amir Town","Aria Mohalla","Arya Mohalla","Asghar Mall","Asghar Mall Scheme","Askari 1","Askari 2","Askari 3","Askari 4","Askari 5","Askari 6","Askari 7","Askari 8","Askari 9","Askari 10","Askari 11","Askari 12","Askari 13","Askari 14","Askari Town","Ayub National Park Area","Babu Lal Hussain Road","Bahria Town - Phase 1","Bahria Town - Phase 2","Bahria Town - Phase 3","Bahria Town - Phase 4","Bahria Town - Phase 5","Bahria Town - Phase 6","Bahria Town - Phase 7","Bahria Town - Phase 8","Bahria Town Phase 1 to 6","Bahria Town Phase 7 to 8","Bakramandi","Banni Chowk","Baraf Khana","British Homes","Carriage Factory Area","Chaklala - Scheme 1","Chaklala - Scheme 2","Chaklala - Scheme 3","Chaklala Cantt","Chaklala Scheme 1","Chaklala Scheme 2","Chaklala Scheme 3","Chakri Road","Chamanzar Colony","Chandni Chowk","Chaur Harpal","China Market","Circular Road","Civil Lines","College Road","Commercial Market (Satellite Town)","Committee Chowk","DHA - Phase 1","DHA - Phase 2","Dhamial Camp","Dhamial Road","DHA Phase 1 (Rawalpindi)","DHA Phase 2 (Rawalpindi)","Dhoke Ali Akbar","Dhoke Babu Irfan","Dhoke Banaras","Dhoke Chaudrian","Dhoke Chiraghdin","Dhoke Dalal","Dhoke Farman Ali","Dhoke Gangal","Dhoke Gujran","Dhoke Hameeda","Dhoke Hassu","Dhoke Kala Khan","Dhoke Kashmirian","Dhoke Khabba","Dhoke Mangtal","Dhoke Matkal","Dhoke Munshi","Dhoke Mustaqeem","Dhoke Naju","Dhoke Paracha","Dhoke Ratta","Dhoke Ratta - Allahbad","Dhoke Ratta - Dhoke Hassu","Dhoke Ratta - Dhoke Ratta","Dhoke Ratta - Kalyamabad","Dhoke Ratta - Railway Colony","Dhoke Sayedan","Dhoke Syedan","Dhowk Sayedan","Eid Gah","Eidgah Road","Faizabad Chowk Area","Fazaia Colony","Fort Road","Gawalmandi","Ghauri Town - Phase 1","Ghauri Town - Phase 2","Ghauri Town - Phase 3","Ghauri Town - Phase 4","Ghauri Town - Phase 5","Ghauri Town - Phase 6","Ghauri Town - Phase 7","Girja Road","Gohar Save Scheme","Gulbahar Scheme","Gulistan Colony","Gulraiz - Phase 1","Gulraiz - Phase 2","Gulraiz - Phase 3","Gulraiz - Phase 4","Gulraiz Housing Scheme (Phase 1 to 8)","Gulshan-e-Abad","Gulshan-e-Khudadad","Gulshan-e-Saeed","Gulshan Abad","Gulshan Dadan Khan","Gulzar-e-Quaid","Harley Street","High Court Road","Highway - Khanna","Highway - Korang Town","Highway - Lohi Bhair","Highway - Pakistan Town","Highway - Sohan","Highway - Zaraj Housing Society","Husseinabad","Imambargah Road","Jalal Din Town","Jamarghata","Jhanda Chichi","Jhelum Road","Judicial Town","Kahuta Road","Kalma Chowk","Kartarpura","Kartar Pura","Kashmiri Mohalla","Kashmir Road","Kattarian","Khanna Dak","Khanna Kak","Khanna Pul","Khayaban-e-Sir Syed (Sector 1 to 4)","Khayaban e Sir Syed","Khurram Colony","Kohati Bazaar","KRL Road","Kuri Road","Lalazar (New \/ Old)","Lalazar - New Lalazar","Lalazar - Old Lalazar","Lalkurti","Liaquat Bagh Area","Liaquat Colony","Madina Colony","Makkah Town","Mall Road Cantt","Marir","Marir Hassan","Media Town Rawalpindi","Millat Colony","Misrial Road","Misriyal - Allama Iqbal Colony","Misriyal - Chakra","Misriyal - Chrisitan Colony","Misriyal - Dhamial Road","Misriyal - Dhoke Gujran","Misriyal - Friends Colony","Misriyal - Girja Road","Misriyal - Madina Colony","Misriyal - Misrial Road","Misriyal - Nai Abadi","Misriyal - PIA Colony","Misriyal - Railway Scheme 7","Misriyal - Siham","Mohan Pura","Morgah","Morgah - Morgah","Moti Bazaar","Munawar Colony","Murree Road","Muslim Town","Nawababad","Naya Nazimabad Rawalpindi","New Afzal Town","New Lalazar","Nishtar Colony","Officers Colony Cantt","Old Airport Area","Peshawar Road","Pindora","Pirwadhai","Police Foundation","Potohar Town","PWD Road","Qasim Market Cantt","Quaid-e-Azam Colony","Railway Colony Cantt","Railway Scheme 7","Raja Bazaar","Raja Bazar","Range Road","Rawal Road","Rawal Road - Awan Town","Rawal Road - Chah Sultan","Rawal Road - Iqbal Town","Rawal Road - Madina Town","Rawat","Rehmanabad","Saddar","Saddar Cantt (Bank Road, Haider Road, Adamjee Road)","Sadiqabad","Sadiqabad - Affandi Colony","Sadiqabad - Bilal Colony","Sadiqabad - Dhoke Kala Khan","Sadiqabad - Dhoke Paracha","Sadiqabad - Muslim Town","Safari Villas","Safari Villas (1, 2, 3)","Saidpur Road","Satellite Town","Satellite Town (Block A, B, C, D, E, F)","Scheme 3 Cantt","Shabbir Town","Shadman Town","Shaheen Town","Shah Khalid Colony","Shakrial","Shalley Valley","Shamsabad","Sher Zaman Colony","Sikandarabad","Sixth Road","Stadium Road","T&T Colony","Tariqabad","Tench Bhatta","Tulsa Road","Urdu Bazaar Rawalpindi","Wahdat Colony","Waris Khan","Westridge 1","Westridge 2","Westridge 3","Zaffar-ul-Haq Road","Zia-ul-Haq Colony"],
            "Renala Khurd": ["Canal Colony","G.T. Road","Main Bazaar","Military Farms Area","Shergarh Road"],
            "Risalpur": ["Industrial Estate","Main Bazaar","Mardan Road","Nowshera Road","PAF Academy Area"],
            "Sadiqabad": ["Fauji Fertilizer Colony (FFC)","Ghalla Mandi","Kamyab Colony","KLP Road","Main Bazaar","Manthar Road","Model Town","Rahim Yar Khan Road","Railway Road","Sanjarpur Road","Tillu Road"],
            "Sahiwal": ["Adda Mai","Al-Raza Colony","Alawal Colony","Al Haseeb Town","Al Janett Colony","Arifwala Road","Baber Colony","Barkat Town","Bashir Town","Bhutto Nagar","Bungla Road","Canal Colony","Chak 82\/6R","Chak 89\/6R","Circular Road","Civil Lines","College Town","Commercial Area","Daudpur","District Courts Area","Farid Town","Fateh Sher Colony","Ganj Shakar Colony","General Bus Stand Area","Ghala Mandi","Gulshan-e-Noor","Haseeb Town","Heera Mandi","Hussain Colony","Jail Road","Jewan City","Jinnah Town","Johar Town","Ladu Basti","Lahore-Multan Road","Lalazar Colony","Liaquat Bazaar","Madina Colony","Main Bazaar","Main Market","Model Town","Muhammadi Colony","Muhammad Nagar","Mujahid Colony","Nai Abadi","New Sahiwal","Noor Shah Road","Old Harappa Road","Pakpattan Road","Police Lines Area","Rafi Gardens","Railway Colony","Sabir Town","Saddar Bazaar","Sadiq Town","Sadman Town","Saeed Ullah Mokal Colony","Satellite Town","Shadab Town","Shadman Town","Shah Din","Stadium Road","Tariq Bin Ziad Colony","Tariq Bin Ziyad Colony","Wahdat Colony","Wapda Colony","Zaheer Nagar"],
            "Sambrial": ["Airport Road","Daska Road","Dry Port Area","Main Bazaar","Sialkot Road","Wazirabad Road"],
            "Samundri": ["Faisalabad Road","Gojra Road","Grain Market","Kutchery Road","Main Bazaar","Rajana Road","Tandlianwala Road"],
            "Sanghar": ["Khipro Road","Main Bazaar","Mirpur Khas Road","Nawabshah Road","Sinjhoro Road"],
            "Sangla Hill": ["Faisalabad Road","Main Bazaar","Pindi Bhattian Road","Safdarabad Road","Shahkot Road"],
            "Sarai Alamgir": ["Canal Road","Cantt Area","G.T. Road","Jhelum River Side","Main Bazaar","Mirpur Road"],
            "Sargodha": ["47 Pull","49 Tail","Al-Rehman City","Allama Iqbal Colony","Ashiana Housing Scheme","Awan Abad","Aziz Bhatti Town","Block 1 to 30","Block X","Block Y","Block Z","Cantonment","Cantt (Sargodha Cantonment)","Cheema Colony","Civil Lines","Club Road","DHA Sargodha","Dhudi Colony","Eagle City Housing Project","Eden Life Housing Scheme","Faisalabad Road","Farooq Colony","Fatima Jinnah Colony","G.T. Road Sargodha","General Bus Stand Area","Gulshan-e-Bashir","Gulshan-e-Hayat Colony","Hameed Town","Iqbal Colony","Islam Pura","Istaqlal Abad Colony","Jhal Chakian Road","Jinnah Colony","Johar Colony","Kachehri Bazaar","Khayaban-e-Naveed","Khayaban-e-Sher","Kot Fareed","Lahore Road","Liaquat Market","Luqman","Madina Colony","Mahil Town","Maryam Town","Model Town","Muslim Town","National Town","Nawab City","New Civil Lines","New Satellite Town","Niazi Colony","Officers Colony","PAF Base Mushaf Area","PAF Colony","Peer Muhammad Colony","Qadirabad Colony","Qartaba Town","Railway Colony","Raza Garden Phase 1","Rehman Pura","Remount Depot Area","Sargodha","Satellite Town (Block A to D)","Shaheen Colony","Shaheen Park","Sillanwali Road","Stadium Road","Sujanpur","Superior Town","Tariqabad","Trust Plaza Area","University Road","Wapda Colony","Waris Town","Zafar Colony"],
            "Sehwan Sharif": ["Dargah Lal Shahbaz Qalandar Area","Indus Highway","Lal Bagh","Main Bazaar","Station Road"],
            "Shahdadkot": ["Garhi Khairo Road","Jacobabad Road","Kambar Road","Main Bazaar"],
            "Shahdadpur": ["Main Bazaar","Railway Station Area","Sanghar Road","Sarhari Road","Tando Adam Road"],
            "Shahkot": ["Faisalabad Road","Lahore Road","Main Bazaar","Nankana Road","Sangla Hill Road"],
            "Shahpur": ["Bhera Road","Khushab Road","Main Bazaar","Sargodha Road"],
            "Shakargarh": ["Kachehri Road","Main Bazaar","Narowal Road","Railway Road","Zafarwal Road"],
            "Sharaqpur": ["Faizpur Interchange Area","Lahore-Jaranwala Road","Main Bazaar","Sheikhupura Road"],
            "Sheikhupura": ["Al-Rehman Garden Sheikhupura","Allama Iqbal Park Area","Baddo Murade","Bhatti Colony","Bhatti Dhilwan","Bhikhi Road","Cantt Area","Chak 12","Chappa","Chowk Pir Bahar Shah","Civil Lines","Company Bagh Area","Defence Colony","Dera Dhilwan","Distt Courts Area","E Community Housing Society","Farooqabad Road","Ferozewala Road","G.T. Road","Garden Town","General Bus Stand Area","Ghang Road","Gloria Colony","Gulshan Colony","Hiran Minar Road","Housing Colony","Housing Colony No. 1","Housing Colony No. 2","Jandiala Road","Jinnah Park Area","Kala Shah Kaku Area","Lahore Road","Main Bazaar","Mairajpura","Mandiala","Mandiali","Mansoorabad","Model Town","Mohalla Farooq Ganj","Mohalla Marajpura","Mohalla Sultanpur","Mujahid Nagar","Nabi Pura","New Civil Lines","Old City","Quaid-e-Azam Colony","Railway Colony","Rasool Nagar","Rehmanpura","Sargodha Road","Sharaqpur","Sharif Colony","Sharqpur Road","Stadium Road","Sultan Colony Residences","Sultanpura","Sun City","Tariq Road","Tup Dar","Virk Colony","Wapda Colony","Zafar Abad"],
            "Shikarpur": ["Cutchery Road","Hathi Gate","Hazari Gate","Jacobabad Road","Karan Bagh","Lakhidar","Main Bazaar","Station Road","Stuart Gunj","Sukkur Road"],
            "Shorkot": ["Cantt Area","Haveli Bahadur Shah Area","Jhang Road","Kabirwala Road","Main Bazaar","PAF Base Rafiqui Area","Shorkot City"],
            "Shujaabad": ["Grain Market","Jalpur Road","Main Bazaar","Multan Road","Railway Station Road","Shujat Khan Tomb Area"],
            "Sialkot": ["Abbot Road","Addah","Adda Pasrurian","Aik Bridge Area","Al-Madina Town","Allama Iqbal Chowk","Allama Iqbal Town","Arazi Yaqub","Askari Colony","Bano Bazaar","Beerh","Begwal","Bhalli Town","Bhoth","Bonkan","Cantt (Sialkot Cantonment)","Chak Mandhar","Chitti Sheikhan","Circular Road","Citi Housing Sialkot","Civil Lines","Commissioner Road","Daluwali","Daska Road","Defence Road","DHA Sialkot","Dheera Sandha","Dhudianwali","Do Darwaza","Fatehgarh","G.T. Road Sialkot","Ghuinke","Gohadpur","Greenwood Street","Gulshan Iqbal","Hajipura","Haripur","Harrison Road","Hunter Pura","Imam Sahib Road","Islamia College Road","Jail Road","Jamke Cheema Town","Jhai","Kais","Kalaswala Town","Kamanwala","Kashmir Road","Khadim Ali Road","Kharota Syedan","Khichian","Kotli Behram","Kotli Loharan","Kutchery Road","Langeriali","Machhi Khokar","Magtown","Marakiwal","Merajke","Miani","Model Town","Mubarakpura","Mughalpura","Mughal Town Gunjianwala","Murray College Road","Muslim Town","Muzaffar Pur","Nekapura","Nishat Park Area","Pakka Garha","Paris Road","Pasrur Road","Poorani Mandi","Prem Nagar","Pul Aik","Raja Bazaar","Rangpura","Rasool Nagar","Saddar Bazaar Cantt","Sambrial Road","Satellite Town","Shahabpura","Shatabgarh","Sialkot Cantt","Sialkot City","Small Industrial Estate","Sohawa","Sublime Chowk Area","Tajpura","Trunk Bazaar","Tufail Kot","Ugoke","Wazirabad Road","Zafarwal Road"],
            "Sibi": ["Cantt Area","Luni Road","Main Bazaar","Railway Colony","Station Road"],
            "Sillanwali": ["Farooq Colony","Main Bazaar","Mandi Town","Sargodha Road","Shaheen Abad Road"],
            "Skardu": ["Airport Road","Gamba Skardu","Hussainabad","Link Road","Main Bazaar","Sadpara Road","Shangrila Road","Yadgar Chowk"],
            "Sujawal": ["Badin Road","Jati Road","Main Bazaar","Shah Bandar Road","Thatta Road"],
            "Sukkur": ["Abdullah Shar","Agri Eng Workshop Residential Colony","Airport Road","Arain Mohalla","Ayub Gate Area","Bachal Shah Miani","Bagh Hayat Ali Shah","Barrage Colony","Barrage Road","Bunder Road","Cantt (Sukkur Cantonment)","City Bypass","Clock Tower Area","Dolphin Chowk Area","G.E.C.H.S","Garam Gaudi","Gharibabad","Golimar Area","Gol Market","Gulshan-e-Iqbal","Hussainabad","Industrial Area Sukkur","Jaffarabad","Lab-e-Mehran","Loco Shed Colony","Main Bazaar","Makka Goth","Malook Shar","Memon Mohalla","Miandad Khan Khoso","Miani Road","Military Road","Minara Road","Mirch Bazaar","Mission Road","Modern Colony","Muslim Colony","Nasirabad","Nawa Goth","New Goth","New Pind","Nishtar Road","Old Sukkur","Panjrapole","Pir Murad Shah Colony","Professors Colony","Public School Area","Queens Road","Railway Colony","Rohan Pura","Sarafa Bazaar","Sasti Basti","Shahi Bazaar","Shah Khalid Colony","Shikarpur Road","Site Area Sukkur","Soomar Goth","Station Road","Sukkur Cooperative Housing Society","Sukkur IBA Campus Area","Sukkur Township","Takkar Muhalla","Thermal Colony","Wari Tar","Workshop Road"],
            "Swabi": ["Gadoon Amazai","Jehangira Road","Karnal Sher Khan Kalay","Kotha","Main Bazaar","Maneri","Mardan Road","Marghuz","Shah Mansoor","Topi Road","Yar Hussain","Zaida"],
            "Swat": ["Ahingaro Dherai","Amankot","Babuzai","Balogram","Barikot","Bazaar Mingora","College Colony","Dangram","Enzaro Dherai","Faizabad","Gulkada (1, 2, 3)","Haji Baba","Islampur","Kabal","Kanju (Township)","Kokarai","Landikas","Malook Abad","Marghazar Road","Mian Baba","Mingora City","Mula Baba","Nawakalay","Panr","Qambar","Rahimabad","Saidu Sharif","Shahdara","Sooray","Spal Bandai","Takhta Band","Tindodag","Watkay"],
            "Talagang": ["Chakwal Road","Chinji Road","Main Bazaar","Mianwali Road","Rawalpindi Road"],
            "Tandlianwala": ["Faisalabad Road","Grain Market","Main Bazaar","Okara Road","Railway Road","Samundri Road","Sugar Mills Colony"],
            "Tando Adam": ["Bhit Shah Road","Hyderabad Road","Johar Colony","Main Bazaar","Mirpurkhas Road","Muhammadi Colony","Station Road"],
            "Tando Allahyar": ["Hyderabad Road","Main Bazaar","Mehran Sugar Mills Area","Mirpurkhas Road","Station Road","Usman Shah Huri Road"],
            "Taunsa Sharif": ["Barthi Road","College Road","D.G. Khan Road","Indus Highway","Kot Qaisrani","Kutchery Road","Main Bazaar","Mangrotha Road"],
            "Taxila": ["Ahatta","Bhir Mound Area","Cantt Area","City Center","Civil Hospital Area","Engineers Colony","Faisal Shaheed Road","G.T. Road Taxila","Gulshan-e-Saeed","Heavy Mechanical Complex (HMC) Colony","HIT Colony","Industrial Estate","Jinnah Colony","Kohsar Colony","Main Bazaar","Model Town","Museum Road","Muslim Colony","Nawababad","Officers Colony","Peshawar Road","Railway Station Road","Sarai Khola","Sir Syed Colony","Station Road","Timber Market","UET Taxila Campus Area","Ziarat Area"],
            "Thatta": ["Keenjhar Lake Road","Main Bazaar","Makli","National Highway","Sujawal Road"],
            "Toba Tek Singh": ["Canal Colony","Civil Lines","College Road","Gojra","Gojra Road","Grain Market","Housing Colony","Kamalia","Kamalia Road","Main Bazaar","Model Town","Pir Mahal Road","Railway Road","Rajana Road","Shorkot Road","Toba Tek Singh"],
            "Topi": ["GIK Institute Area","Main Bazaar","Swabi Road","Tarbela Dam Road"],
            "Turbat": ["Absor Road","Airport Road","Dannuk","Ginna","Hospital Road","Kech River Area","Main Bazaar","Malikabad","Zangiabad"],
            "Uch Sharif": ["Alipur Road","Bahawalpur Road","Dera Nawab Road","Main Bazaar","Tomb of Bibi Jawindi Area"],
            "Umerkot": ["Chachro Road","Dhoronaro Road","Main Bazaar","Mirpur Khas Road","Umerkot Fort Area"],
            "Usta Muhammad": ["Dera Allah Yar Road","Gandakha Road","Jafarabad Road","Main Bazaar"],
            "Vehari": ["Burewala","Burewala Road","Club Road","College Road","Danewal","Grain Market","Karkhana Bazaar","Luddan Road","Mailsi","Main Bazaar","Multan Road","Officers Colony","People's Colony","Sharqi Colony","Vehari"],
            "Wah Cantt": ["Aslam Market","Babu Mohalla","Basti","Cantt Area","CB Colony","Chachi Mohalla","Education City","Farooq Colony","G.T. Road Wah","Gudwal","Gulshan Colony","Heavy Industries Taxila Area","Hospital Road","Lala Rukh (Colony)","Lalarukh Block A to D","Lalazar Colony","Lossar","Main Commercial Area","Model Town","Munirabad","Muslim Nagar","Nawababad","Officers Colony","POF Colony (Sector 1 to 24)","Saddar Bazaar","Sanitary Colony","Shahwali Colony","Station Road","Target Range Area","Timber Market","Upper Cap Area"],
            "Wazirabad": ["Alipur Chattha Road","Circular Road","Dhaunkal Road","G.T. Road","Kutchery Road","Main Bazaar","Mohalla Shish Mahal","Nizamabad","Railway Road","Sialkot Road","Sohdra"],
            "Yazman": ["Ahmedpur Road","Bahawalpur Road","Kudwala Road","Main Bazaar"],
            "Zafarwal": ["Main Bazaar","Narowal Road","Pasrur Road","Shakargarh Road"],
            "Zahir Pir": ["Chachran Sharif Road","Khanpur Road","KLP Road","Main Bazaar","Motorway M-5 Interchange"],
            "Zhob": ["Airport Road","Appozai","Cantt Area","D.I. Khan Road","Main Bazaar","Quetta Road"],
            "Nawabshah": ["Gharibabad","Hospital Road","Kazi Ahmed Road","Line Par","Main Bazaar","Manuabad","Monotechnic Road","Quaid-e-Awam University Area","Sakrand Road","Sanghar Road","Society Area","Taj Colony"],
            "Wah": ["Aslam Market","Babu Mohalla","Basti","Cantt Area","CB Colony","Chachi Mohalla","Education City","Farooq Colony","G.T. Road Wah","Gudwal","Gulshan Colony","Heavy Industries Taxila Area","Hospital Road","Lala Rukh (Colony)","Lalarukh Block A to D","Lalazar Colony","Lossar","Main Commercial Area","Model Town","Munirabad","Muslim Nagar","Nawababad","Officers Colony","POF Colony (Sector 1 to 24)","Saddar Bazaar","Sanitary Colony","Shahwali Colony","Station Road","Target Range Area","Timber Market","Upper Cap Area"],
            "Wah Cantonment": ["Aslam Market","Babu Mohalla","Basti","Cantt Area","CB Colony","Chachi Mohalla","Education City","Farooq Colony","G.T. Road Wah","Gudwal","Gulshan Colony","Heavy Industries Taxila Area","Hospital Road","Lala Rukh (Colony)","Lalarukh Block A to D","Lalazar Colony","Lossar","Main Commercial Area","Model Town","Munirabad","Muslim Nagar","Nawababad","Officers Colony","POF Colony (Sector 1 to 24)","Saddar Bazaar","Sanitary Colony","Shahwali Colony","Station Road","Target Range Area","Timber Market","Upper Cap Area"],
            "D.G. Khan": ["Al-Ghazi Town","Al-Madina Colony","Block 1 to 24","Cantt Area","Chah Pehalwan","Circular Road","Civil Lines","College Road","DG Cement Road","Faridi Bazar","Gadhai","General Bus Stand Area","Gulshan-e-Iqbal","Housing Scheme (1, 2)","Indus Highway","Jampur Road","Jinnah Colony","Katchery Road","Khyaban-e-Sarwar","Kot Chutta Road","Kotla Shikani","Liaquat Town","Madina Colony","Main Bazaar","Model Town","Multan Road","New Housing Scheme","Officers Colony","Old City","Pathan Colony","Pir Qattal Road","Pull Chorrah","Railway Colony","Rajanpur Road","Saddar Bazaar","Sakhi Sarwar Road","Samina Chowk Area","Shadman Town","Shah Sikandar","Taunsa Road","Vital Colony"],
            "D.I. Khan": ["Adda Daraban","Airport Area","Al-Qaim Colony","Bannu Road","Basti Ustrana","Cantt (DI Khan Cantonment)","Circular Road","Civil Lines","Commissionery Bazaar","Degree College Area","Dera Town","Eidgah Road","Gomal University Campus","Grid Station Road","Gulshan-e-Habib","Haq Nawaz Park Area","Indus River Road","Islamia Colony","Kanwer Ram Road","Khyber Bazaar","Kotli Imam Hussain","Main Bazaar","Mandi Road","Moaza Dinpur","Model Town","Muslim Bazaar","Naqvi Town","Paroa Road","Peshawar Road","Qasba Shorkot","Rahim Bazaar","Railway Colony","Ratta Kulachi","Saddar Bazaar","Shorkot Road","Stadium Road","Tank Road","Topanwala Chowk Area","Town Hall Area","University Road","Zafar Colony"],
            "New Mirpur City": ["Allama Iqbal Road","Bank Hurmat","Bradford Chowk Area","Cantt Area","Chaksawari Road","Chechian","Chowk Shaheedan","Degree College Area","DHA Mirpur","F\/1","F\/2","F\/3","F\/4","G\/1","G\/2","G\/3","G\/4","Industrial Estate","Islamgarh Road","Jari Kas","Kalyal","Khadan","Kotli Road","Main Bazaar","Mangla Road","Mian Muhammad Town","Model Town","Naangi","New City (Sector B1 to B4)","New Mirpur City","Officers Colony","Paniola Road","Pullo Manda","Quaid-e-Azam Stadium Area","Sector A1 to A4","Sector B1 to B4","Sector C1 to C4","Sector D1 to D4","Stadium Road","Thothal"],
            "Mirpur (AJK)": ["Allama Iqbal Road","Bank Hurmat","Bradford Chowk Area","Cantt Area","Chaksawari Road","Chechian","Chowk Shaheedan","Degree College Area","DHA Mirpur","F\/1","F\/2","F\/3","F\/4","G\/1","G\/2","G\/3","G\/4","Industrial Estate","Islamgarh Road","Jari Kas","Kalyal","Khadan","Kotli Road","Main Bazaar","Mangla Road","Mian Muhammad Town","Model Town","Naangi","New City (Sector B1 to B4)","New Mirpur City","Officers Colony","Paniola Road","Pullo Manda","Quaid-e-Azam Stadium Area","Sector A1 to A4","Sector B1 to B4","Sector C1 to C4","Sector D1 to D4","Stadium Road","Thothal"],
            "Kotli (AJK)": ["Gulhar Sharif","Housing Scheme","Khuiratta Road","Main Bazaar","Mirpur Road","Mohalla Shaheedan","Rawalpindi Road"]
        };

        function $id(id) { return document.getElementById(id); }

        function sortAZ(list) {
            return list.slice().sort(function(a, b) {
                return a.localeCompare(b, 'en', { sensitivity: 'base', numeric: true });
            });
        }

        function normalizeCity(name) {
            return (name || '').toLowerCase()
                .replace(/\(.*?\)/g, '')
                .replace(/[^a-z0-9]/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();
        }

        function getAreasForCity(cityName) {
            if (!cityName) return [];
            cityName = cityName.trim();

            // 1. Exact match
            if (pakistanSpecificCityAreas[cityName]) {
                return sortAZ(pakistanSpecificCityAreas[cityName]);
            }
            // 2. Case-insensitive exact match
            var lower = cityName.toLowerCase();
            for (var key in pakistanSpecificCityAreas) {
                if (pakistanSpecificCityAreas.hasOwnProperty(key)) {
                    if (key.toLowerCase() === lower) {
                        return sortAZ(pakistanSpecificCityAreas[key]);
                    }
                }
            }
            // 3. Normalized match (ignores parenthesized text, extra punctuation)
            var norm = normalizeCity(cityName);
            if (norm) {
                for (var key2 in pakistanSpecificCityAreas) {
                    if (pakistanSpecificCityAreas.hasOwnProperty(key2)) {
                        if (normalizeCity(key2) === norm) {
                            return sortAZ(pakistanSpecificCityAreas[key2]);
                        }
                    }
                }
            }

            // For smaller towns / tehsils not listed with detailed sub-localities:
            // Realistic local options without any fake colonies
            return sortAZ([
                cityName + " City / Main Town",
                "Main Bazaar",
                "Bypass / Highway Road",
                "Railway Station Road",
                "General Bus Stand / Adda",
                "Civil Hospital / THQ / DHQ Area",
                "Kutchery / Tehsil Courts Area",
                "College / School Road"
            ]);
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
