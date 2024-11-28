export interface Expense {
    id: number;
    amount: string;
    description: string;
    category: number;
    expense_date: string;
    imageName?: string;

}

export interface ApiResponse {
    data: Expense[];
}