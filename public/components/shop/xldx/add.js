Vue.component('Add', {
	template: `
		<el-drawer title="添加"  direction="rtl" size="1200px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="车位编号" prop="cewei_id">
							<el-select  remote :remote-method="remoteCeweiidList"  style="width:100%" v-model="form.cewei_id" filterable clearable placeholder="请选择车位编号">
								<el-option v-for="(item,i) in cewei_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用定义" prop="fydy_id">
							<el-select @change="selectFybz_id"  style="width:100%" v-model="form.fydy_id" filterable clearable placeholder="请选择费用定义">
								<el-option v-for="(item,i) in fydy_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用标准" prop="fybz_id">
							<el-select   style="width:100%" v-model="form.fybz_id" filterable clearable placeholder="请选择费用标准">
								<el-option v-for="(item,i) in fybz_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="生成方式" prop="cwfy_scfs">
							<el-radio-group v-model="form.cwfy_scfs">
								<el-radio :label="1">按月生成</el-radio>
								<el-radio :label="2">按日生成</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.cwfy_scfs == 2">
					<el-col :span="24">
						<el-form-item label="生成类型" prop="cwfy_sclx">
							<el-radio-group v-model="form.cwfy_sclx">
								<el-radio :label="1">【按每月30天计算】</el-radio>
								<el-radio :label="2">【按每月实际天数计算】</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.cwfy_scfs == 2">
					<el-col :span="24">
						<el-form-item label="开始时间" prop="cwfy_kstime">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.cwfy_kstime" clearable placeholder="请输入开始时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.cwfy_scfs == 2">
					<el-col :span="24">
						<el-form-item label="终止时间" prop="cwfy_zztime">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.cwfy_zztime" clearable placeholder="请输入终止时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.cwfy_scfs == 1">
					<el-col :span="24">
						<el-form-item label="开始月份" prop="cwfy_ksmonth">
							<el-date-picker value-format="yyyy-MM" type="month" v-model="form.cwfy_ksmonth" clearable placeholder="请输入开始月份"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.cwfy_scfs == 1">
					<el-col :span="24">
						<el-form-item label="终止月份" prop="cwfy_zzmonth">
							<el-date-picker value-format="yyyy-MM" type="month" v-model="form.cwfy_zzmonth" clearable placeholder="请输入终止月份"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="多选" prop="dxxx">
							<el-checkbox-group v-model="form.dxxx">
								<el-checkbox key="0" label="1">男</el-checkbox>
							</el-checkbox-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="多选2" prop="duoxuan">
							<el-checkbox-group v-model="form.duoxuan">
								<el-checkbox v-for="(item,i) in duoxuans" :key="i" :label="item.val.toString()">{{item.key}}</el-checkbox>
							</el-checkbox-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="文件" prop="xldx_wenjian">
							<Upload v-if="show" size="small" file_type="file"     :file.sync="form.xldx_wenjian"></Upload>
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
	},
	data(){
		return {
			form: {
				cewei_id:'',
				fydy_id:'',
				fybz_id:'',
				cwfy_scfs:1,
				cwfy_sclx:1,
				cwfy_kstime:curentTime(),
				cwfy_zztime:'',
				cwfy_ksmonth:curentTime(),
				cwfy_zzmonth:'',
				member_id:'',
				dxxx:[],
				duoxuan:[],
				xldx_wenjian:'',
				member_id:'',
			},
			cewei_ids:[],
			fydy_ids:[],
			fybz_ids:[],
			duoxuans:[],
			loading:false,
			rules: {
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Xldx/getFieldList').then(res => {
					if(res.data.status == 200){
						this.fydy_ids = res.data.data.fydy_ids
						this.duoxuans = res.data.data.duoxuans
					}
				})
			}
		}
	},
	methods: {
		open(){
			const myURL = new URL(window.location.href)
			const urlobj = param2Obj(myURL.href)
			this.form.member_id = urlobj.member_id
		},
		selectFybz_id(val){
			this.form.fybz_id = ''
			axios.post(base_url + '/Xldx/getFybz_id',{fydy_id:val}).then(res => {
				if(res.data.status == 200){
					this.fybz_ids = res.data.data
				}
			})
		},
		remoteCeweiidList(val){
			axios.post(base_url + '/Xldx/remoteCeweiidList',{queryString:val}).then(res => {
				if(res.data.status == 200){
					this.cewei_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Xldx/add',this.form).then(res => {
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
			this.cewei_ids = []
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
