import { createDateColumn, createNumberColumn, createSelectionColumn, createTextColumn } from '@/components/data-table/column-def';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Batch } from '@/types';
import { router } from '@inertiajs/react';
import { ColumnDef } from '@tanstack/react-table';
import { Eye, MoreHorizontal, Pencil, Trash } from 'lucide-react';
import { toast } from 'sonner';

export const batchColumns: ColumnDef<Batch>[] = [
    createSelectionColumn<Batch>(),
    createTextColumn<Batch>('batch_number', 'Batch Number'),
    createNumberColumn<Batch>('quantity_received', 'Quantity Received'),
    createNumberColumn<Batch>('current_quantity', 'Current Quantity'),
    createDateColumn<Batch>('manufacture_date', 'Manufacture Date'),
    createDateColumn<Batch>('expiry_date', 'Expiry Date'),
    createTextColumn<Batch>('medicine_name', 'Medicine'),
    createTextColumn<Batch>('supplier_name', 'Supplier'),
    createDateColumn<Batch>('created_at', 'Created'),
    createDateColumn<Batch>('updated_at', 'Updated'),
    {
        id: 'actions',
        cell: ({ row }) => {
            const batch = row.original;

            return (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="ghost" className="h-8 w-8 p-0">
                            <span className="sr-only">Open menu</span>
                            <MoreHorizontal className="h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuLabel>Actions</DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem>
                            <Eye className="h-4 w-4" />
                            <span>View</span>
                        </DropdownMenuItem>
                        <DropdownMenuItem>
                            <Pencil className="h-4 w-4" />
                            <span>Edit</span>
                        </DropdownMenuItem>
                        <AlertDialog>
                            <AlertDialogTrigger asChild>
                                <DropdownMenuItem variant="destructive" onSelect={(e) => e.preventDefault()}>
                                    <Trash className="h-4 w-4" />
                                    <span>Delete</span>
                                </DropdownMenuItem>
                            </AlertDialogTrigger>
                            <AlertDialogContent>
                                <AlertDialogHeader>
                                    <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                                    <AlertDialogDescription>
                                        This action cannot be undone. This will permanently delete the batch.
                                    </AlertDialogDescription>
                                </AlertDialogHeader>
                                <AlertDialogFooter>
                                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                                    <AlertDialogAction
                                        onClick={() => {
                                            router.delete(`/stock/${batch.id}`, {
                                                onSuccess: () => {
                                                    toast.success('Batch deleted successfully');
                                                },
                                            });
                                        }}
                                    >
                                        Delete
                                    </AlertDialogAction>
                                </AlertDialogFooter>
                            </AlertDialogContent>
                        </AlertDialog>
                    </DropdownMenuContent>
                </DropdownMenu>
            );
        },
    },
];

// Default visibility for responsive design
export const batchColumnVisibility = {
    batch_number: true,
    quantity_received: true,
    current_quantity: true,
    manufacture_date: false,
    expiry_date: true,
    medicine_name: true,
    supplier_name: false,
    created_at: false,
    updated_at: false,
};
