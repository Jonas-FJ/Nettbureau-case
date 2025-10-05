# Nettbureau case <small style="font-size: small">av Jonas Fjærestad</small>

## Oppsett
Last ned prosjektet, koden skal kjøres via kommando linje så husk hvor den lagres. Koden er laget på php versjon 8.4.13 vet ikke om den kjører på eldre versjoner.

## Fil-strukturen
**Øverste mappe** inneholder intigrasjonskoden, en logging kode, en log fil og en log fil for feilkoder.  
**Vendor** mappen er all api kode og modeller lastet ned fra den offisielle githuben til pipedrive.  
**Create** mappen inneholder funksjonene som oppretter Leads, Organisasjoner og personer.  
**DuplicateCheck** inneholder funksjoner som sjekker om Leads, Organisasjoner eller Personer finnes fra før av.  
**update** inneholder funksjoner som oppdaterer lead og person.  
**testData** inneholder php filer med data til testing.

## Kjøring og testing av kode
Koden kjøres fra kommando linjen  
``php pipedrive_lead_intefrasjon.php "test variabel" "test fil"``

``"test variabel"``: Her legges det inn en av fire variabler:  
- ``a``: Brukes for å teste kode der all dataen er ny.
- ``b``: Brukes for å teste kode der organisasjon blir opprettet før intigrasjonskoden kjøres, derved simulerer at organisasjonen eksisterer fra før, men ikke person, eller lead.
- ``c``: Brukes for å teste kode der person blir opprettet før intigrasjonskoden, derved simulerer at personen finnes fra før, men ikke organisasjon eller lead.
- ``d``: Brukes for å teste kode der all dataen finnes fra før.

``"test fil"`` er der det legges inn en path til hvilken test data fil som skal brukes  

For eksempel ``php pipedrive_lead_intefrasjon.php a testData\new_data_test.php``  
### Test data filene forklart:
> **OBS** Filene kan bare brukes en gang siden de baserer seg på at dataen ikke finnes fra før utenom **pre_existing_data_test.php** dem kan alltid brukes for å teste sammen med ``d``.  
Etter at de har blitt brukt en gang må minst variablen 'name' endres i testfilene. Teknisk sett kan hvilken som helst fil brukes til hvilken som helst test, etter de er brukt opp. Kravet er bare at navnet må være unik på test a,b og c

**new_data_test.php:** brukes sammen med ``a``  
**org_existing_test.php:** brukes sammen ``b``  
**person_existing_test.php:** brukes sammen med ``c``  
**pre_existing_data_test.php:** brukes sammen med ``d``
