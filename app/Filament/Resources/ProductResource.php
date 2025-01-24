<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-battery-0';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Product Information')->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $operation, $state, Set $set) => $operation
                                === 'create' ? $set('slug', Str::slug($state)) : null),
                        Forms\Components\TextInput::make('slug')
                            ->disabled()
                            ->required()
                            ->unique(Product::class, 'slug', ignoreRecord: true)
                            ->dehydrated(),
                        Forms\Components\MarkdownEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
                    Section::make('Images')->schema([
                        Forms\Components\FileUpload::make('image')
                            ->directory('products')
                            ->multiple()
                            ->maxFiles(5)
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),
                ])->columnSpan(2),
                Group::make()->schema([
                    Section::make('Price')->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('₱'),
                    ]),
                    Section::make('Association')->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required(),
                        Forms\Components\Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->required(),
                    ]),
                    Section::make('Status')->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->required(),
                        Forms\Components\Toggle::make('in_stock')
                            ->default(true)

                            ->required(),
                        Forms\Components\Toggle::make('is_featured')
                            ->default(false)

                            ->required(),
                        Forms\Components\Toggle::make('on_sale')
                            ->default(false)
                            ->required(),
                    ])
                ])->columnSpan(1),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('in_stock')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\IconColumn::make('on_sale')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
