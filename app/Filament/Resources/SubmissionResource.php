<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubmissionResource\Pages;
use App\Models\Submission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ExportAction;
use App\Filament\Exports\SubmissionExporter;
class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('fields.name')
                    ->required(),
                Forms\Components\TextInput::make('fields.phone')
                    ->required(),
                Forms\Components\TextInput::make('fields.email')
                    ->required(),
                Forms\Components\Textarea::make('fields.message')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fields.name')
                    ->label('Name'),
                Tables\Columns\TextColumn::make('fields.phone')
                    ->label('Phone'),
                Tables\Columns\TextColumn::make('fields.email')
                    ->label('Email'),
                Tables\Columns\TextColumn::make('fields.message')
                    ->label('Message'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(SubmissionExporter::class),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
        ;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubmissions::route('/'),
            'create' => Pages\CreateSubmission::route('/create'),
            // 'edit' => Pages\EditSubmission::route('/{record}/edit'),
        ];
    }
}
