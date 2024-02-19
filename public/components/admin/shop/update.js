Vue.component('Update', {
	template: `
		<el-drawer title="修改"  direction="rtl" size="1420px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="公司名称" prop="shop_name">
							<el-input  v-model="form.shop_name" autoComplete="off" clearable  placeholder="请输入公司名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="企业地址" prop="shop_address">
							<shengshiqu v-if="show" :checkstrictly="{ checkStrictly: false }" :type="1" :treeoption.sync="form.shop_address"></shengshiqu>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="详细地址" prop="shop_range">
							<el-input  v-model="form.shop_range" autoComplete="off" clearable  placeholder="请输入详细地址"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="联系人员" prop="shop_xlr">
							<el-input  v-model="form.shop_xlr" autoComplete="off" clearable  placeholder="请输入联系人员"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="联系电话" prop="shop_tel">
							<el-input  v-model="form.shop_tel" autoComplete="off" clearable  placeholder="请输入联系电话"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="公司邮箱" prop="shop_email">
							<el-input  v-model="form.shop_email" autoComplete="off" clearable  placeholder="请输入公司邮箱"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="开始日期" prop="start_date">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.start_date" clearable placeholder="请输入开始日期"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="到期日期" prop="end_date">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.end_date" clearable placeholder="请输入到期日期"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="购买功能" prop="goumai">
							<el-select   style="width:100%" v-model="form.goumai" filterable clearable placeholder="请选择购买功能">
								<el-option v-for="(item,i) in goumais" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="小区上限" prop="restrict_num">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.restrict_num" clearable :min="0" placeholder="请输入小区上限"/>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" style="text-align:center;margin:0 0 30px 0">
				<el-button :size="size" style="width:35%;" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" style="width:35%;" @click="closeForm">取 消</el-button>
			</div>
			</div>
		</el-drawer>
	`
	,
	components:{
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
		info: {
			type: Object,
		},
	},
	data(){
		return {
			form: {
				shop_name:'',
				shop_address:[],
				shop_range:'',
				shop_xlr:'',
				shop_tel:'',
				shop_email:'',
				start_date:curentTime(),
				end_date:'',
				goumai:'',
			},
			goumais:[],
			loading:false,
			rules: {
				shop_name:[
					{required: true, message: '公司名称不能为空', trigger: 'blur'},
				],
				shop_tel:[
					{pattern:/^1[3456789]\d{9}$/, message: '联系电话格式错误'}
				],
				shop_email:[
					{pattern:/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/, message: '公司邮箱格式错误'}
				],
				start_date:[
					{required: true, message: '开始日期不能为空', trigger: 'blur'},
				],
				end_date:[
					{required: true, message: '到期日期不能为空', trigger: 'blur'},
				],
				goumai:[
					{required: true, message: '购买功能不能为空', trigger: 'change'},
				],
				restrict_num:[
					{required: true, message: '小区上限不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '小区上限格式错误'}
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Shop/getFieldList').then(res => {
					if(res.data.status == 200){
						this.goumais = res.data.data.goumais
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			this.form.start_date = parseTime(this.form.start_date)
			this.form.end_date = parseTime(this.form.end_date)
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Shop/update',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
