import { Component } from '@angular/core';
import { NgForm } from '@angular/forms';

import { ApiResponse, Expense } from './expense';
import { ExpenseService } from './expense.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
})
export class AppComponent {
  expense: Expense = {
    id: 0,
    amount: '',
    description: '',
    category: 0,
    expense_date: '',
    imageName: '',
  };

  expenses: Expense[] = [];
  selectedExpense: Expense = { ...this.expense }; // For editing
  selectedFile: File | null = null;

  isEditModalOpen = false;

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
      },
    });
  }

  private resetAlerts(): void {
    this.error = '';
    this.success = '';
  }

  addExpense(f: NgForm): void {
    this.resetAlerts();

    this.expenseService.add(this.expense, this.selectedFile).subscribe({
      next: (res: any) => {
        if (res.data) {
          this.expenses.push(res.data);
          this.success = 'Expense added successfully';
          f.reset();
          this.selectedFile = null; // Reset the file selection
          this.getExpense(); // Refresh the expense list
        } else {
          this.error = 'Invalid response format';
        }
      },
      error: (err) => {
        console.error('Error:', err);
        this.error = err.message || 'An error occurred';
      },
    });
  }

  deleteExpense(id: number): void {
    this.expenseService.deleteExpense(id).subscribe({
      next: (response) => {
        if (response.status === 204) {
          // Remove the expense from the list
          this.expenses = this.expenses.filter((expense) => expense.id !== id);
          this.success = 'Expense deleted successfully';
        } else {
          this.error = 'Unexpected response from the server';
        }
      },
      error: (err) => {
        console.error('Error deleting expense:', err);
        this.error = err.message || 'Failed to delete expense';
      },
    });
  }

  openEditModal(expense: Expense): void {
    this.selectedExpense = { ...expense }; // Clone the expense to avoid two-way binding issues
    this.isEditModalOpen = true;
  }

  closeEditModal(): void {
    this.isEditModalOpen = false;
    this.selectedFile = null; // Reset selected file
  }

  updateExpense(f: NgForm): void {
    this.resetAlerts();

    this.expenseService.editExpense(this.selectedExpense, this.selectedFile).subscribe({
      next: (res) => {
        this.success = 'Expense updated successfully';
        this.getExpense(); // Refresh the list
        this.closeEditModal();
      },
      error: (err) => {
        this.error = err.message || 'Failed to update expense';
      },
    });
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.selectedFile = input.files[0];
    }
  }
}