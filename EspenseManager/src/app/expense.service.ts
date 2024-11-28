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

  add(expense: Expense, image: File | null): Observable<ApiResponse> {
    const formData = new FormData();
    formData.append('amount', expense.amount);
    formData.append('description', expense.description);
    formData.append('expense_date', expense.expense_date);
    if (image) {
      formData.append('image', image); 
    }
  
    return this.http.post<ApiResponse>(`${this.apiUrl}/add_expense.php`, formData);
  }

  deleteExpense(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/delete_expense.php?id=${id}`, {
      headers: new HttpHeaders({
        'Accept': 'application/json',
      }),
      withCredentials: false,
      observe: 'response', // Capture the full response
    });
  }

  editExpense(expense: Expense, image: File | null): Observable<any> {
    const formData = new FormData();
    formData.append('id', expense.id.toString());
    formData.append('amount', expense.amount);
    formData.append('description', expense.description);
    formData.append('expense_date', expense.expense_date);
    if (image) {
      formData.append('image', image);
    }
  
    return this.http.post(`${this.apiUrl}/edit.php`, formData, {
      headers: new HttpHeaders({
        Accept: 'application/json',
      }),
      withCredentials: false, // Ensure CORS headers are handled by the server
    });
  }
}