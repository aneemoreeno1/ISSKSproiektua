# Taldekideen izenak
  Uxue Aurtenetxe Maortua

  Aimar Basterretxea Zubizarreta

  Yoel Justel Morala

  Ane Moreno Ruiz

  Xinyan Wang


# Docker bidez proiektua hedatzeko instrukzioak

1. Biltegia klonatu:
```bash
  $ git clone https://github.com/aneemoreeno1/ISSKSproiektua.git
  ```
2. Karpeta barruan sartu:
```bash
  $ cd ISSKSproiektua
  ```
3.  Entregaren adarrera aldatu (Kasu honetan _entrega_1_ adarra):
```bash
  $ git checkout entrega_3
  ```
4. Web irudia sortu:
```bash
$ docker build -t web .
  ```
5. _docker_compose_ komandoa erabiliz, zerbitzua altzatu:
 ```bash
  $ docker-compose up -d
  ```
6. Datu-basea behar bezala inportatzeko *phpMyAdmin* erbili:
   - Nabigatzailea ireki eta hurrengo helbidean sartu:
     - http://localhost:8890/

   - Log-ina bete:
     - Username: **admin**
     - Password: **test**
     
   - Datu-basea inportatu:
     - _database_ datubasean sakatu (ezkerraldean dago)
     - _import_ atalean sakatu
     - Gure _entrega_1_ adarraren barruan dagoen *_database.sql_* fitxategia inportatu
       - **OSO GARRANTZITSUA!** Bestela, honako errorea agertuko zaigu :

           ``
            'database.usuarios doesn't exist'
            ``
  7. Web-sisteman satu:
     - Dena ondo eginez gero, hurrengo helbidean sartuz, pelikulak gordetzeko web sistema irekiko da:
       - http://localhost:81/ edo https://localhost:8443/
       
          - Baldin eta web sisteman sartzean _Internal Server Error_ errorea ematen bada, hurrengoa egin beharko litzateke:
            - Edukiontzi barruan sartu:
            ```bash
            $ docker exec -it issksproiektua-web-1 /bin/bash
            ```
           - Behin barruan:
            `` root@d4e5d31e2f07:/var/www/html# `` 
              - _mod_rewrite_ modulua aktibatu behar da. URL edo URL berridazketak erabiltzeko aukera ematen duena:
             ```bash
              $ a2enmod rewrite
             ```
              - Apache berrabiarazi:
              ```bash
              $ service apache2 restart
              ```
          - Berriro zerbitzua altzatu:
            ```bash
            $ docker-compose up -d
            ```
          - Pelikuen web sisteman sartu:
             - http://localhost:81/ edo https://localhost:8443/
               
  8. Web sisteman sartzean alerta mezua agertuko da '_Advertencia: riesgo potencial de seguridad a continuación_'

     - '**Avanzado...**' botoia sakatu behar da.

        - Jarraian '**Aceptar el riego y continuar**' botoia hautatu.

          - Nahi izanez gero, ziurtagiria ikusteko aukera egongo da '_Ver certificado_' botoian sakatuz.
     
  10. '**Aceptar el riego**' botoia sakatzean dena ondo badoa zuzenan web sistemako hasierako orrialdera bistaratuko da ( http://localhost:81/ -n edo https://localhost:8443/ -en).

        - Web-sisteman sartzen saiatzean sistema ez bada ondo abiatzen edo erroreak agertzen badira, hurrengo urratsak jarraitu:
          
            - Edukiontziak gelditu:
            ```bash
             $ docker-compose down
            ```
            - MySQL karpeta ezabatu:
              ```bash
              sudo rm -rf mysql/
              ```
            - Web zerbitzuaren irudia ezabatu:
              ```bash
              docker rmi web
              ```
            - Irudia berriro eraiki:
              ```bash
              docker build -t web .
              ```
            - Zerbitzuak berriro altxatu:
              ```bash
              docker-compose up -d
              ```
          - Zerbitzuak berriro altxatzean, dena ondo dagoela egiaztatzeko Web-sistemaren hasierako  orrian sartu
              - https://localhost:8443/  edo http://localhost:81/
  1. Edukiontziak itzali eta ezabatzeko (ez ditu fitxategiak ezabatzen, ezta datu-basea ere, edukiontzitik kanpo gordeta daudelako):
  ```bash
  $ docker-compose down
  ```
