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
  $ git checkout entrega_1
  ```
4. Web irudia sortu:
```bash
$ docker build -t="web" .
  ```
5. _docker_compose_ komandoa erabiliz, zerbitzua altzatu:
 ```bash
  $ docker-compose up -d
  ```
6. Datu-basea behar bezala importatzeko *phpMyAdmin* erbili:
   - Nabigatzailea ireki eta hurrengo helbidean sartu:
     - http://localhost:8890/

   - Log-ina bete:
     - Username: **admin**
     - Password: **test**
     
   - Datu-basea inportatu:
     - _import_ atalean sakatu
     - Gure _entrega_1_ adarraren barruan dagoen *_database.sql_* fitxategia inportatu
       - **OSO GARRANTZITSUA!** Bestela, honako errorea agertuko zaigu :

           ``
            'database.usuarios doesn't exist'
            ``
  7. Web-sisteman satu:
     - Dena ondo eginez gero, hurrengo helbidean sartuz, pelikulak gordetzeko web sistema irekiko da:
       - http://localhost:81/
       
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
             - http://localhost:81/
               
  8. Edukiontziak itzaili eta ezabatzeko (ez ditu fitxategiak ezabatzen, ezta datu-basea ere, edukiontzitik kanpo gordeta daudelako):
  ```bash
  $ docker-compose down
  ```
