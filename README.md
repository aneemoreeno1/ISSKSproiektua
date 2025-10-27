# Taldekideen izenak
  Uxue Aurtenetxe Maortua

  Aimar Basterretxea Zubizarreta

  Yoel Justel Morala

  Ane Moreno Ruiz

  Xinyan Wang


# Docker bidez proiektua hedatzeko instrukzioak

1. Biltegia klonatu
```bash
  $ git clone https://github.com/aneemoreeno1/ISSKSproiektua.git
  ```
2. Karpeta barruan sartu
```bash
  $ cd ISSKSproiektua
  ```
3.  Entregaren adarrera aldatu (_Kasu honetan entrega_1_ adarra)
```bash
  $ git checkout entrega_1
  ```
4. _docker_compose_ komandoa erabiliz, zerbitzua altzatu
 ```bash
  $ docker-compose up -d
  ```
5. Datu-basea behar bezala importatzeko *phpMyAdmin* erbili:
  5.1 Nabigatzailea ireki eta sartu hurrengo helbidean:
  
       - http://localhost:8890/
       
   5.2 Log ina bete:
   
       - Username: *admin*
       - Password: *test*
   
   5.3 Datu-basea inportatu:
   
     - _import_ atalean sakatu
     
     - Gure entrega_1 adarraren barruan dagoen *_database.sql_* fitxategia inportatu
       *!* Hau egitea oso garrantzitsua da. Bestela honako errorea agertuko zaigu :
           '_database.usuarios doesn't exist_'
           
  6. Web-sisteman satu:
    6.1 Dena ondo eginez gero, hurrengo helbidean sartuz, pelikulak gordetzeko web sistema irekiko da:
    
       - http://localhost:81/
       
  7. Edukiontziak itzaili eta ezabatzeko (ez ditu fitxategiak ezabatzen, ezta datu-basea ere, edukiontzitik kanpo gordeta daudelako):
  ```bash
  $ docker-compose down
  ```

