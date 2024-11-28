import { Component } from '@angular/core';
import { NgForm } from '@angular/forms';

import { ApiResponse, Expense } from './expense';
import { ExpenseService } from './expense.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  expense: Expense = {
    id: 0,
    amount: '',
    description: '',
    category: 0,
    expense_date: ''
  };

  expenses: Expense[] = [];

  error = '';
  success = '';

  constructor(private expenseService: ExpenseService) {}

  ngOnInit() {
    this.getExpense();
  }

  getExpense(): void {
    this.expenseService.getAll().subscribe({
      next: (response: ApiResponse) => {
        this.expenses = response.data;
        this.success = 'Expenses retrieved successfully';
      },
      error: (err) => {
        console.error('Error fetching expenses:', err);
        this.error = err.message || 'Failed to fetch expenses';
      }
    });
  }

  private resetAlerts(): void {
    this.error = '';
    this.success = '';
  }

  addExpense(f: NgForm) {
    this.resetAlerts();

    this.expenseService.add(this.expense).subscribe({
      next: (res: any) => {
        if (res.data) {
          this.expenses.push(res.data);
          this.success = 'Expense added successfully';
          f.reset();
          this.getExpense(); // Refresh the expense list
        } else {
          this.error = 'Invalid response format';
        }
      },
      error: (err) => {
        console.error('Error:', err);
        this.error = err.message || 'An error occurred';
      }
    });
  }

  deleteExpense(id: number): void {
    this.expenseService.deleteExpense(id).subscribe({
      next: () => {
        this.expenses = this.expenses.filter(expense => expense.id !== id);
        this.success = 'Expense deleted successfully';
      },
      error: (err) => {
        console.error('Error:', err);
        this.error = err.message || 'Failed to delete expense';
      }
    });
  }

  editExpense(expense: Expense): void {
    this.expense = { ...expense };
  }
}