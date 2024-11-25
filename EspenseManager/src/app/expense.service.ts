import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { ApiResponse } from './expense';
import { Expense } from './expense';
import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class ExpenseService {
  // Note the full URL to the API
  private apiUrl = 'http://localhost/expenseapi';

  constructor(private http: HttpClient) { }

  getAll(): Observable<any> {
    return this.http.get(`${this.apiUrl}/list_expense.php`, {
      headers: new HttpHeaders({
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }),
      withCredentials: false 
    });
  }

  add(expense: Expense): Observable<ApiResponse> {
    return this.http.post<ApiResponse>(`${this.apiUrl}/add_expense.php`, expense);
  }

  deleteExpense(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/expense/${id}`);
  }
}