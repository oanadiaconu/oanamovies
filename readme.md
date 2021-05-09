# Oana Movies

Aplicatia consta intr-un blog wordpress ce genereaza articole automat cu cele mai populare filme.

## Descriere problema

In cadrul blogului am creat un script ce se conecteaza la API-urile TMDB si OMDB pentru a culege diverse detalii despre filme
Blog-ul este folositor pentru pasionatii de filme ce sunt in cautare de noi surse de entertainment.
Aplicatia creaza automat articole pe baza celor mai populare filme si adauga review-uri precum si locul de unde pot fi vizualizate / cumparate.

## Descriere API

API-ul TMDB pune la dispozitie o lista de metode prin care se pot culege informatii despre filme - este o alternativa la iMDB.
Din acest API am folosit mai multe metode pentru a crea scriptul de generare articole automate
Au fost folosite endpoint-urile:
- Get Movie List (/genre/movie/list) pentru a extrage categoriile de filme
- Get Popular (/movie/popular) pentru a extrage liste cu cele mai populare filme
- Get Review (/movie/{movie_id}/reviews) pentru a extrage review-uri despre filme
- Get Watch Providers (/movie/{movie_id}/watch/providers) o lista cu locurile din care filmele pot fi achizitionate / vizualizate

API-ul OMDB a fost folosit in completarea celui anterior - contine informatii mai detaliate despre anumite caracteristici ale filmelor.
Am folosit endpoint-ul de extragere al detaliilor pe baza titlului de film (prin utilizarea parametrului t={movie_name})

## Flux de date
Initial a fost extrasa o lista de filme populare.
Pentru TMDB metoda de autentificare aleasa a fost prin adaugarea unui Bearer Token in header-ul request-ului, prin cheia Authorization.
Get Popular Request/Response, Metoda HTTP "GET"
```
GET Request - No body
URL: https://api.themoviedb.org/3/movie/popular?page=20

Response:
{
    "page": 20,
    "results": [
        {
            "adult": false,
            "backdrop_path": "/paxjgQ7I2oALdi85F5qw8PBiO2q.jpg",
            "genre_ids": [
                28,
                27
            ],
            "id": 648043,
            "original_language": "en",
            "original_title": "The Driver",
            "overview": "In a zombie apocalypse, one man desperately tries to keep his family alive.",
            "popularity": 85.642,
            "poster_path": "/qT5YjDsz5Ud7OHXyDvqtrMZXsdE.jpg",
            "release_date": "2019-11-25",
            "title": "The Driver",
            "video": false,
            "vote_average": 6.5,
            "vote_count": 49
        },
        {
            "adult": false,
            "backdrop_path": "/dIWwZW7dJJtqC6CgWzYkNVKIUm8.jpg",
            "genre_ids": [
                10749,
                16,
                18
            ],
            "id": 372058,
            "original_language": "ja",
            "original_title": "君の名は。",
            "overview": "High schoolers Mitsuha and Taki are complete strangers living separate lives. But one night, they suddenly switch places. Mitsuha wakes up in Taki’s body, and he in hers. This bizarre occurrence continues to happen randomly, and the two must adjust their lives around each other.",
            "popularity": 85.368,
            "poster_path": "/q719jXXEzOoYaps6babgKnONONX.jpg",
            "release_date": "2016-08-26",
            "title": "Your Name.",
            "video": false,
            "vote_average": 8.6,
            "vote_count": 7415
        },
        ....
    ]
}
```

Informatiile despre filme au fost completate cu un request ulterior in API OMDB.
Pentru autentificare, cheia de autorizare este trimisa aici ca parametru GET "apikey".
```
GET Request - No body
URL: http://www.omdbapi.com/?apikey={api_key}&t=Mortal+Kombat&plot=full

Response:
{
  "Title": "Mortal Kombat",
  "Year": "1995",
  "Rated": "PG-13",
  "Released": "18 Aug 1995",
  "Runtime": "101 min",
  "Genre": "Action, Adventure, Fantasy, Sci-Fi, Thriller",
  "Director": "Paul W.S. Anderson",
  "Writer": "Ed Boon (video games), John Tobias (video games), Kevin Droney",
  "Actors": "Christopher Lambert, Robin Shou, Linden Ashby, Cary-Hiroyuki Tagawa",
  "Plot": "Based on the popular video game of the same name \"Mortal Kombat\" tells the story of an ancient tournament where the best of the best of different Realms fight each other. The goal - ten wins to be able to legally invade the losing Realm. Outworld has so far collected nine wins against Earthrealm, so it's up to Lord Rayden and his fighters to stop Outworld from reaching the final victory...",
  "Language": "English",
  "Country": "USA",
  "Awards": "1 win & 3 nominations.",
  "Poster": "https://m.media-amazon.com/images/M/MV5BNjY5NTEzZGItMGY3My00NzE4LThkYTUtYjJkNzk3MDBiMWE3XkEyXkFqcGdeQXVyNzg5MDE1MDk@._V1_SX300.jpg",
  "Ratings": [
    {
      "Source": "Internet Movie Database",
      "Value": "5.8/10"
    },
    {
      "Source": "Rotten Tomatoes",
      "Value": "44%"
    },
    {
      "Source": "Metacritic",
      "Value": "60/100"
    }
  ],
  "Metascore": "60",
  "imdbRating": "5.8",
  "imdbVotes": "108,001",
  "imdbID": "tt0113855",
  "Type": "movie",
  "DVD": "01 Sep 2009",
  "BoxOffice": "$70,454,098",
  "Production": "New Line Cinema",
  "Website": "N/A",
  "Response": "True"
}
```

Ulterior, tot prin API TMDB sunt extrase review-uri

```
GET Request - No body
URL: https://api.themoviedb.org/3/movie/460465/reviews

Response:
{
    "id": 460465,
    "page": 1,
    "results": [
        {
            "author": "TheDarkKnight31",
            "author_details": {
                "name": "",
                "username": "TheDarkKnight31",
                "avatar_path": null,
                "rating": 10.0
            },
            "content": "I will be short. You should understand how hard to make movies based on such a legendary universe (expectation is too high!), with a lot of characters that need screen time, and with limited budget, PLUS in a pandemic situation, - the director made a great job.\r\n\r\nThis is the best adaptation of such a legendary title.",
            "created_at": "2021-04-12T05:39:44.021Z",
            "id": "6073dd204d0e8d0043061b63",
            "updated_at": "2021-04-27T22:52:50.651Z",
            "url": "https://www.themoviedb.org/review/6073dd204d0e8d0043061b63"
        },
        {
            "author": "garethmb",
            "author_details": {
                "name": "",
                "username": "garethmb",
                "avatar_path": "/https://secure.gravatar.com/avatar/3593437cbd05cebe0a4ee753965a8ad1.jpg",
                "rating": null
            },
            "content": "Mortal Kombat Gives Fans Of The Franchise The Brutal Live-Action Version They Deserve\r\nFans of the Mortal Kombat series have known that the path to bringing the violent and controversial game to live-action formats has been a mixed bag. While the first film in 1995 was a decent hit; the follow-up in 1997 disappointed fans who had grown weary of the PG-13 take on the series.\r\n\r\nSubsequent efforts such as the 2011 television series also left fans wanting more; especially since the game series had become even more graphic and violent.\r\n\r\nAn animated film released in 2020 gave fans a taste of what they wanted as it featured graphical violence which many fans believed was essential to properly catch the spirit and action of the series.\r\n\r\nThe latest offering in the series “Mortal Kombat”; reboots the cinematic universe and gives fans the intense, brutal, and graphic violence that they have demanded. The film keeps the basic premise that the Outworld realm has won nine tournaments in a row, and based on the ancient laws; one more victory would allow them to take control of the Earth.\r\n\r\nRaiden the Thunder God (Tadanobu Asano); who has been tasked with protecting Earth looks to assemble and train a band of champions to save Earth. Naturally, this is not going to be easy as Shang Tsung (Chin Han); is not willing to follow the rules of the tournament and dispatches his top fighter (Sub Zero (Joe Taslim) to dispatch the champions of Earth before the tournament in a clear violation of the rules in order to ensure total victory.\r\n\r\nWhat follows is solid and very graphic action which contains gore and brutality on a level that almost kept the film from earning an R-rating. The action sequences are well-choreographed and there were some great recreations of classic moves by characters from the game series which were really well utilized and did not seem like gratuitous pandering.\r\n\r\nWhile the plot is fairly simplistic and does not deviate greatly from the source material; it does give a larger backstory to the universe. It was really enjoyable to see many nods to the franchise throughout both subtle and overt and while some characters were glaringly absent which was a surprise; the characters that were included were really solid to see and the door was wide open for their inclusion at a later date.\r\n\r\nWhile the cast does not contain any star power in terms of what Western audiences might expect from a major studio release; the ensemble works well and do a great job in bringing their characters to life.\r\n\r\nThe film leaves sequels wide open and teases a character that in my opinion was a glaring omission from the film. That being said; “Mortal Kombat” gives fans a solid adaptation that does not shy away from gore and violence and gives fans the cinematic experience that they have wanted.\r\n\r\n3.5 stars out of 5",
            "created_at": "2021-04-22T23:16:21.703Z",
            "id": "608203c52b8a430029d0a07d",
            "updated_at": "2021-04-22T23:16:21.703Z",
            "url": "https://www.themoviedb.org/review/608203c52b8a430029d0a07d"
        },
        ...
    ]
}
```

Iar in final, locurile de unde pot fi vizualizate / cumparate

```
GET Request - No body
URL: https://api.themoviedb.org/3/movie/460465/watch/providers

Response:
{
    "id": 460465,
    "results": {
        "CA": {
            "link": "https://www.themoviedb.org/movie/460465-mortal-kombat/watch?locale=CA",
            "rent": [
                {
                    "display_priority": 2,
                    "logo_path": "/q6tl6Ib6X5FT80RMlcDbexIo4St.jpg",
                    "provider_id": 2,
                    "provider_name": "Apple iTunes"
                },
                {
                    "display_priority": 38,
                    "logo_path": "/paq2o2dIfQnxcERsVoq7Ys8KYz8.jpg",
                    "provider_id": 68,
                    "provider_name": "Microsoft Store"
                },
                {
                    "display_priority": 68,
                    "logo_path": "/p3Z12gKq2qvJaUOMeKNU2mzKVI9.jpg",
                    "provider_id": 3,
                    "provider_name": "Google Play Movies"
                },
                {
                    "display_priority": 410,
                    "logo_path": "/sVBEF7q7LqjHAWSnKwDbzmr2EMY.jpg",
                    "provider_id": 10,
                    "provider_name": "Amazon Video"
                },
                {
                    "display_priority": 518,
                    "logo_path": "/vDCcryHD32b0yMeSCgBhuYavsmx.jpg",
                    "provider_id": 192,
                    "provider_name": "YouTube"
                }
            ]
        },
        ...
    }
}
```

Informatiile extrase au fost prelucrate si inserate in articole wordpress prin intermediul metodei wp_post_insert

## Capturi ecran
![Homepage](https://oanadiaconu.com/capturi/homepage_blog.png "Homepage")
![Articol automat](https://oanadiaconu.com/capturi/articol_creat_automat.png "Articol automat")
![Review-Disponibilitate](https://oanadiaconu.com/capturi/review_si_disponibilitate_film.png "Review-Disponibilitate")

## Referinte
[DOC API TMDB](https://developers.themoviedb.org/3/getting-started/introduction)
[DOC API OMDB](http://www.omdbapi.com/)
[URL Aplicatie](https://oanadiaconu.com)
