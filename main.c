#include <my_global.h>
#include <mysql.h>
#include <stdio.h>
#include <unistd.h>			//UART
#include <fcntl.h>			//UART
#include <termios.h>		//UART
#include <stdlib.h>
#include <time.h>
#include <pthread.h>

	
	
int uart0_filestream;
unsigned char rx_buffer[256];

void *zapytanie_co_x(void *ptr);

void *symulacja(void *ptr);

void zapis_danych(char wybor_bazy,char parametr_1[], char parametr_2[],MYSQL *con);

void pomiar_temp_wilg(MYSQL *con);

void pomiar_ruch_osw(MYSQL *con);

void finish_with_error(MYSQL *con) 
{
  fprintf(stderr, "%s\n", mysql_error(con));
}
	
void initUART(){
	uart0_filestream = open("/dev/ttyUSB0", O_RDWR | O_NOCTTY );		//Inicjalizacja komunikacji UART
	if (uart0_filestream == -1)
	{
		printf("Error - Unable to open UART.  Ensure it is not in use by another application\n");
	}
	// Ustawienie paramterów komunikacji. 
	struct termios options;							
	tcgetattr(uart0_filestream, &options);
	options.c_cflag = B9600 | CS8 | CLOCAL | CREAD;		
	options.c_iflag = IGNPAR;
	options.c_oflag = 0;
	options.c_lflag = ICANON ;
	tcflush(uart0_filestream, TCIFLUSH);
	tcsetattr(uart0_filestream, TCSANOW, &options);
	
}



	void main(){
		
		
		
		//Zmienne wykorzystywane do tworzenia wątków.
	  pthread_t thread1, thread2, thread3;
	  const int message1 = 1800;		//30min- Interwał czasowy sprawdzania czujnika DHT11.
      const int message2 = 30;			//30sek- Opóznienie- ponowne sprawdzenie natężenie światła po wykryciku ruchu.
      int  iret1, iret2, iret3;
		
	  initUART();			//Inicjalizacja UART.
		
		MYSQL *con = mysql_init(NULL);		//Inicjalizacja MySQL.
		if (con == NULL){
			fprintf(stderr, "%s\n", mysql_error(con));
			exit(1);
		}  

		if (mysql_real_connect(con, "localhost", "mysql_user", "raspberry", 			//Połączenie z bazą danych.
          "moja_baza_danych", 0, NULL, 0) == NULL) {
			finish_with_error(con);
		}    

		// Utworzenie nowego wątku. Wykonuje funkcję zapytanie_co_x przyjmując argument message1 = 1800.
		//Pobranie pomiarów co 30 min.
		iret1 = pthread_create( &thread1, NULL, zapytanie_co_x, (void*) &message1);
		if(iret1){
         fprintf(stderr,"Error - pthread_create() return code: %d\n",iret1);
         exit(EXIT_FAILURE);
		}
		
	/*	//Stworzenie wątku obsługującego symulację.
		iret3 = pthread_create( &thread3, NULL, symulacja, (void*) con);
		if(iret3){
         fprintf(stderr,"Error - pthread_create() return code: %d\n",iret1);
         exit(EXIT_FAILURE);
		}*/
		
		//Sprawdzanie danych przychodzących.
		if (uart0_filestream != -1){
			while(1){
			//Zapisywanie danych z UART w buforze. Funkcja read blokuje program, az do chwili odczytania danych.
			int rx_length = read(uart0_filestream, (void*)rx_buffer, 256);		
			if (rx_length < 0){
			    	printf("Błąd, rx_length<0\n", rx_length);
	
			}else{
				rx_buffer[rx_length] = '\0';		//Zakończenie wiadomości tekstowej.
			}
			
			
			if(rx_buffer[0]=='r'){
				
				//Gdy wykryto ruch w pomieszczeniu. 
				pomiar_ruch_osw(con);
				
				//Stworzenie wątku sprawdzającego stan naświetlenia 30 sekund po wykryciu ruchu.
				iret2 = pthread_create( &thread2, NULL, zapytanie_co_x, (void*) &message2);
				if(iret2){
					fprintf(stderr,"Error - pthread_create() return code: %d\n",iret2);
					exit(EXIT_FAILURE);
				}
				
			}else if(rx_buffer[0]=='t'){
					pomiar_temp_wilg(con);
			}
								
	
		}

	
			//Zakończenie UART oraz MySql
			close(uart0_filestream);
			mysql_close(con);
			exit(0);
		
		}
	
	}	

  void *zapytanie_co_x(void *ptr){
	  
    int *message = (int *) ptr;
    printf("message: %d \n", *message);

   do{
		time_t start_t, end_t;
		double diff_t=0; 
		time(&start_t);
  
	   // Odliczanie czasu do wysłania kolejnego zapytania
		while((int)diff_t<*message){
	
		  time(&end_t);
		  diff_t = difftime(end_t, start_t);
	    }
	  
		//Wysłanie zapytania o stan temp. i wilgotności(t), lub ruchu i natężenia światła(s).
	unsigned char tx_buffer[2];

		if(*message==1800){
		tx_buffer[0] = 't';						
						}else{tx_buffer[0]= 's';};
			
		if (uart0_filestream != -1){
			int count = write(uart0_filestream,&tx_buffer[0], 2);		
			if (count < 0){
				printf("UART TX error\n");
			}
		}   
    }while(*message==1800);
	
}


void zapis_danych(char wybor_bazy, char parametr_1[], char parametr_2[],MYSQL *con){     

	int totalrows=0;
	const char *querry;
	char do_zapisu[50];
					//SFORMUŁOWANIE ZAPYTANIA. WYBOR TABELI DO KTOREJ BEDA ZAPISYWANE DANE
	if(wybor_bazy=='r'){
		querry="SELECT * FROM `ruch`";	
	}else if(wybor_bazy=='t'){
		querry="SELECT * FROM `temp`";		
	}
					// SPRAWDZENIE LICZBY ZAPISANYCH DANYCH W TABELI
		mysql_query(con,querry);	
		MYSQL_RES *confres = mysql_store_result(con);
		totalrows = mysql_num_rows(confres);
		totalrows++;		//OBLICZENIE NASTEPNEGO ID
				
				//SFORMUŁOWANIE POLECENIA ZAPISU DANYCH DO BAZY
		if(wybor_bazy=='r'){	
			sprintf(do_zapisu, "INSERT INTO ruch VALUES(%d,NOW(),%d,%d)",totalrows,atoi(parametr_1),atoi(parametr_2));
		}else if(wybor_bazy=='t'){
			sprintf(do_zapisu, "INSERT INTO temp VALUES(%d,NOW(),%d,%d)",totalrows,atoi(parametr_1),atoi(parametr_2));  
			
		}
			 
	    //ZAPISANIE DANYCHY DO TABELI BAZY DANYCH
  if (mysql_query(con, do_zapisu )) {
      finish_with_error(con);
  }
	
}

void pomiar_temp_wilg(MYSQL *con){
	 
		char temp[5];
		char wilgotnosc[5];
		int	i=1;
		
		while(rx_buffer[i]!=';'){
			
				temp[i-1]=rx_buffer[i];
				i++;
		}
			temp[i-1] = '\0';   //Zakończenie tekstu
			i++;
				while(rx_buffer[i]!=';'){
										
					wilgotnosc[i-7]=rx_buffer[i];
					i++;					
				}
				
					wilgotnosc[i-7] = '\0';   	
											
									//Wywołaniu funkcji zapisu do bazy danych
zapis_danych(rx_buffer[0],temp,wilgotnosc,con);
				
}

void pomiar_ruch_osw(MYSQL *con){
	
		char swiatlo[3]; 
		int i=3;
		char obecnosc[2];
				
		 obecnosc[0]=rx_buffer[1];
		while(rx_buffer[i]!=';'){
			
				swiatlo[i-3]=(int)rx_buffer[i];
					i++;
		}
				swiatlo[i-3] = '\0'; //Zakończenie tekstu
							
							//Wywołaniu funkcji zapisu do bazy danych
zapis_danych(rx_buffer[0],obecnosc,swiatlo,con);
				
}


void *symulacja(void *ptr){
	MYSQL *con = ptr;
	
	while(1){
		float diff_t;
		char nastepny_stan_do_ustawienia;
		//Odczytani natężenia oświetlenie zmierzonego dnia poprzedniego o podobnej godzinie
		//Oraz dokładnej godziny pomiaru dnia poprzedniego
		if (mysql_query(con, "SELECT UNIX_TIMESTAMP(mdate), natezenie_swiatla FROM ruch WHERE mdate>DATE_SUB(TIMESTAMP(NOW()), INTERVAL 24 HOUR)  AND TIME(mdate)>'16:00:00' AND TIME(mdate)<'09:00:00' LIMIT 1;")){
			finish_with_error(con);
		}
		MYSQL_RES *result = mysql_store_result(con);
		MYSQL_ROW row;
		row = mysql_fetch_row(result);
		 //Określenie stanu do ustawienia- '0'jeśli natężenie wynosilo mniej niż 30, '1'jeśli było wieksze bądz równe 30.
		if(atoi(row[1])>=30){
			
		nastepny_stan_do_ustawienia='1';
		
		}else{ nastepny_stan_do_ustawienia='0';}
		 //Poniższa pętla powoduje, że program odczeka z wysterowaniem oświetlenia,
	     //aż do godziny o ktorej dokonano pomiar dnia poprzedniego. UNIX_TIMESTAMP(mdate) jest zapisane w row[0].
		do{
	   
			sleep(1);
			diff_t = difftime((int)time(NULL), atoi(row[0]) );
	    }while(diff_t<86400);
			mysql_free_result(result);
			//Sprawdzenie czy tryb symulacji jest uruchomiony
		if (mysql_query(con, "SELECT symulacja FROM panel_sterowania WHERE ID=0;")){
			finish_with_error(con);
		}
		
		result = mysql_store_result(con);
		if (result == NULL) {
			finish_with_error(con);
		}
  
		row = mysql_fetch_row(result);
		printf("id: %s \n",row[0]);		//UZYWANE DO TESTOW
			//Przesłanie wiadomości o porządanym stanie oświetlenia do arduino, jeżeli tryb symulacji jest włączony
		if(atoi(row[0])==1){ 		//wlaczony tryb symulacji
	  
			unsigned char tx_buffer[2];
			tx_buffer[0]='w';
			tx_buffer[1]=nastepny_stan_do_ustawienia;
			printf("buffer: %s",tx_buffer);		//UZYWANE DO TESTOW
	
			if (uart0_filestream != -1){
				int count = write(uart0_filestream,&tx_buffer[0], 2);		
			}
	  
	  
	  
		}
		mysql_free_result(result);


	}		
	
	
}







